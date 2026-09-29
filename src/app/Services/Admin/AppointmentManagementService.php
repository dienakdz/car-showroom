<?php

namespace App\Services\Admin;

use App\Models\Appointment;
use App\Models\CarUnit;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentManagementService
{
    public const STATUS_LABELS = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'done' => 'Đã hoàn tất',
        'cancelled' => 'Đã hủy',
    ];

    /**
     * @param  array<string, mixed>  $validated
     */
    public function save(array $validated, User $actor, ?Appointment $appointment = null): Appointment
    {
        return DB::transaction(function () use ($validated, $actor, $appointment): Appointment {
            $isNew = $appointment === null || ! $appointment->exists;
            $appointment ??= new Appointment;

            $carUnitId = array_key_exists('car_unit_id', $validated)
                ? $validated['car_unit_id']
                : ($appointment->car_unit_id ?? null);

            /** @var CarUnit|null $unit */
            $unit = ! empty($carUnitId) ? CarUnit::query()->find($carUnitId) : $appointment->carUnit;

            $trimId = array_key_exists('trim_id', $validated)
                ? $validated['trim_id']
                : ($unit instanceof CarUnit ? $unit->trim_id : ($appointment->trim_id ?? null));

            $lead = null;
            $leadId = $validated['lead_id'] ?? $appointment->lead_id;

            if (! empty($validated['customer_mode']) && $validated['customer_mode'] === 'new_lead' && ! empty($validated['customer_name']) && ! empty($validated['customer_phone'])) {
                $lead = Lead::query()->create([
                    'name' => trim((string) $validated['customer_name']),
                    'phone' => trim((string) $validated['customer_phone']),
                    'email' => ! empty($validated['customer_email']) ? trim((string) $validated['customer_email']) : null,
                    'source' => 'unit_detail',
                    'status' => 'booked',
                    'car_unit_id' => $unit instanceof CarUnit ? $unit->id : null,
                    'trim_id' => $trimId,
                    'assigned_to' => $validated['handled_by'] ?? $actor->id,
                    'message' => 'Lịch hẹn xem xe / lái thử tại Showroom' . (! empty($validated['note']) ? ': ' . trim((string) $validated['note']) : ''),
                ]);
            } elseif (! empty($leadId)) {
                $lead = Lead::query()->find($leadId);
            } elseif (! empty($validated['user_id'])) {
                $lead = Lead::query()
                    ->where('user_id', $validated['user_id'])
                    ->whereNotIn('status', ['closed', 'lost'])
                    ->latest('id')
                    ->first();
            }

            $payload = Arr::only($validated, [
                'user_id',
                'car_unit_id',
                'lead_id',
                'handled_by',
                'scheduled_at',
                'status',
                'note',
            ]);

            if ($trimId !== null && ($isNew || array_key_exists('car_unit_id', $validated) || array_key_exists('trim_id', $validated))) {
                $payload['trim_id'] = $trimId;
            }

            if ($lead !== null) {
                $payload['lead_id'] = $payload['lead_id'] ?? $lead->id;
                $payload['user_id'] = $payload['user_id'] ?? $lead->user_id;
                $payload['car_unit_id'] = $payload['car_unit_id'] ?? $lead->car_unit_id;
                $payload['trim_id'] = $payload['trim_id'] ?? $lead->trim_id ?? $trimId;
                $payload['handled_by'] = $payload['handled_by'] ?? $appointment->handled_by ?? $lead->assigned_to ?? $actor->id;
            }

            $payload['handled_by'] = $payload['handled_by'] ?? $appointment->handled_by ?? $actor->id;

            $oldStatus = $appointment->status;

            $appointment->fill($payload);
            $appointment->save();

            if ($isNew) {
                try {
                    $customerName = $lead !== null ? (string) $lead->name : 'Khách hàng';
                    $timeStr = $appointment->scheduled_at ? Carbon::parse($appointment->scheduled_at)->format('H:i d/m/Y') : '';
                    app(\App\Services\Admin\NotificationService::class)->notifyAdmins(
                        'appointment',
                        'Lịch hẹn lái thử / xem xe mới',
                        "Lịch hẹn của {$customerName} lúc {$timeStr}.",
                        route('admin.appointments.index'),
                        'fa fa-calendar-check',
                        ['appointment_id' => $appointment->id]
                    );
                } catch (\Throwable) {
                }
            }

            if ($lead !== null) {
                $currentStatus = (string) $appointment->status;
                $statusMap = self::STATUS_LABELS;

                if ($isNew) {
                    $timeStr = $appointment->scheduled_at ? Carbon::parse($appointment->scheduled_at)->format('H:i d/m/Y') : 'Chưa rõ';
                    $statusName = $statusMap[$currentStatus];
                    LeadNote::query()->create([
                        'lead_id' => $lead->id,
                        'created_by' => $actor->id,
                        'note' => "Đã tạo lịch hẹn xem xe / lái thử (#{$appointment->id}) lúc {$timeStr} [{$statusName}].",
                    ]);
                } elseif ($oldStatus !== $currentStatus) {
                    $from = $statusMap[$oldStatus];
                    $to = $statusMap[$currentStatus];
                    LeadNote::query()->create([
                        'lead_id' => $lead->id,
                        'created_by' => $actor->id,
                        'note' => "Lịch hẹn #{$appointment->id} chuyển trạng thái: [{$from}] ➔ [{$to}].",
                    ]);
                }

                if ($currentStatus !== 'cancelled' && ! in_array($lead->status, ['closed', 'lost'], true)) {
                    $lead->update(['status' => 'booked']);
                }
            }

            // Gửi thông báo đến tài khoản khách hàng khi trạng thái lịch hẹn thay đổi
            $customerUser = $appointment->user ?? ($appointment->user_id ? User::find($appointment->user_id) : ($lead?->user_id ? User::find($lead->user_id) : null));
            if ($customerUser instanceof User && ! $isNew && $oldStatus !== (string) $appointment->status) {
                try {
                    $timeStr = $appointment->scheduled_at ? Carbon::parse($appointment->scheduled_at)->format('H:i d/m/Y') : 'thời gian hẹn';
                    $carInfo = $unit instanceof CarUnit && $unit->stock_code ? " ({$unit->stock_code})" : '';
                    $currentStatus = (string) $appointment->status;

                    $statusNotificationConfig = [
                        'confirmed' => [
                            'title' => 'Lịch hẹn lái thử đã được xác nhận',
                            'message' => "Lịch hẹn lái thử / xem xe{$carInfo} lúc {$timeStr} đã được Showroom xác nhận. Hân hạnh được đón tiếp bạn!",
                            'icon' => 'fa-solid fa-calendar-check',
                        ],
                        'cancelled' => [
                            'title' => 'Lịch hẹn lái thử đã bị hủy',
                            'message' => "Lịch hẹn lái thử / xem xe{$carInfo} lúc {$timeStr} đã được hủy.",
                            'icon' => 'fa-solid fa-calendar-xmark',
                        ],
                        'done' => [
                            'title' => 'Lịch hẹn lái thử đã hoàn tất',
                            'message' => "Showroom cảm ơn bạn đã đến trải nghiệm xe lúc {$timeStr}. Đội ngũ tư vấn sẵn sàng hỗ trợ bạn bất kỳ thông tin nào tiếp theo.",
                            'icon' => 'fa-solid fa-circle-check',
                        ],
                    ];

                    if (isset($statusNotificationConfig[$currentStatus])) {
                        $cfg = $statusNotificationConfig[$currentStatus];
                        app(\App\Services\Admin\NotificationService::class)->notifyUser(
                            $customerUser,
                            'appointment',
                            $cfg['title'],
                            $cfg['message'],
                            route('account.show', ['tab' => 'appointments']),
                            $cfg['icon'],
                            ['appointment_id' => $appointment->id, 'status' => $currentStatus]
                        );
                    }
                } catch (\Throwable) {
                }
            }

            $fresh = $appointment->fresh([
                'user',
                'carUnit.trim.model.make',
                'carUnit.primaryMedia',
                'trim.model.make',
                'lead',
                'handledBy',
            ]);

            return $fresh ?? $appointment;
        });
    }

    /**
     * Cập nhật nhanh trạng thái lịch hẹn.
     */
    public function updateStatus(Appointment $appointment, string $status, User $actor): Appointment
    {
        return $this->save(['status' => $status], $actor, $appointment);
    }
}
