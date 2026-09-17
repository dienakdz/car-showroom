<?php

namespace App\Services\Admin;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Support\Arr;
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

            $lead = null;
            $leadId = $validated['lead_id'] ?? $appointment->lead_id;

            if (! empty($validated['customer_mode']) && $validated['customer_mode'] === 'new_lead' && ! empty($validated['customer_name']) && ! empty($validated['customer_phone'])) {
                $leadSource = ! empty($validated['car_unit_id'])
                    ? 'unit_detail'
                    : (! empty($validated['trim_id']) ? 'trim_page' : 'contact');

                $lead = Lead::query()->create([
                    'name' => trim((string) $validated['customer_name']),
                    'phone' => trim((string) $validated['customer_phone']),
                    'email' => ! empty($validated['customer_email']) ? trim((string) $validated['customer_email']) : null,
                    'source' => $leadSource,
                    'status' => 'booked',
                    'car_unit_id' => $validated['car_unit_id'] ?? null,
                    'trim_id' => $validated['trim_id'] ?? null,
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
                'trim_id',
                'lead_id',
                'handled_by',
                'scheduled_at',
                'status',
                'note',
            ]);

            if ($lead !== null) {
                $payload['lead_id'] = $lead->id;
                $payload['user_id'] = $payload['user_id'] ?? $lead->user_id;
                $payload['car_unit_id'] = $payload['car_unit_id'] ?? $lead->car_unit_id;
                $payload['trim_id'] = $payload['trim_id'] ?? $lead->trim_id;
                $payload['handled_by'] = $payload['handled_by'] ?? $lead->assigned_to ?? $actor->id;
            }

            $payload['handled_by'] = $payload['handled_by'] ?? $actor->id;

            $oldStatus = $appointment->status;

            $appointment->fill($payload);
            $appointment->save();

            if ($lead !== null) {
                $currentStatus = (string) $appointment->status;
                $statusMap = self::STATUS_LABELS;

                if ($isNew) {
                    $timeStr = optional($appointment->scheduled_at)->format('H:i d/m/Y') ?? 'Chưa rõ';
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
