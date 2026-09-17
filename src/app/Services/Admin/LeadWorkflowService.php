<?php

namespace App\Services\Admin;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LeadWorkflowService
{
    public const VALID_STATUSES = ['new', 'contacted', 'qualified', 'booked', 'closed', 'lost'];

    public const STATUS_LABELS = [
        'new' => 'Mới tiếp nhận',
        'contacted' => 'Đã liên hệ',
        'qualified' => 'Khách tiềm năng',
        'booked' => 'Thương thảo / Lịch hẹn',
        'closed' => 'Chốt giao dịch',
        'lost' => 'Hủy / Thất bại',
    ];

    public const SOURCE_LABELS = [
        'unit_detail' => 'Chi tiết xe trên Web',
        'trim_page' => 'Trang thông số phiên bản',
        'finance' => 'Hỗ trợ tính toán trả góp',
        'trade_in' => 'Thu cũ đổi mới',
        'contact' => 'Form liên hệ showroom',
    ];

    public const STAGE_NEW = 'new';

    public const STAGE_CONSULTING = ['contacted', 'qualified'];

    public const STAGE_NEGOTIATING = 'booked';

    public const STAGE_CLOSED = 'closed';

    public const STAGE_LOST = 'lost';

    /**
     * Cập nhật thông tin chi tiết và phân công của Lead.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateLead(Lead $lead, array $data, User $actor): Lead
    {
        return DB::transaction(function () use ($lead, $data, $actor): Lead {
            $payload = Arr::only($data, [
                'name',
                'phone',
                'email',
                'assigned_to',
                'status',
                'message',
                'car_unit_id',
                'trim_id',
            ]);

            if (isset($payload['name'])) {
                $payload['name'] = trim((string) $payload['name']);
            }
            if (isset($payload['phone'])) {
                $payload['phone'] = trim((string) $payload['phone']);
            }
            if (array_key_exists('email', $payload)) {
                $payload['email'] = filled($payload['email']) ? trim((string) $payload['email']) : null;
            }
            if (array_key_exists('message', $payload)) {
                $payload['message'] = filled($payload['message']) ? trim((string) $payload['message']) : null;
            }
            if (array_key_exists('assigned_to', $payload)) {
                $payload['assigned_to'] = (int) ($payload['assigned_to'] ?? 0) > 0 ? (int) $payload['assigned_to'] : null;
            }

            $oldStatus = $lead->status;
            $oldAssignedTo = $lead->assigned_to;

            $lead->fill($payload);
            $lead->save();

            // Nếu thay đổi trạng thái, tự động ghi log vào LeadNote
            if (isset($payload['status']) && $payload['status'] !== $oldStatus) {
                $from = self::STATUS_LABELS[$oldStatus];
                $to = self::STATUS_LABELS[$payload['status']] ?? (string) $payload['status'];

                LeadNote::query()->create([
                    'lead_id' => $lead->id,
                    'created_by' => $actor->id,
                    'note' => "Chuyển giai đoạn từ [{$from}] sang [{$to}].",
                ]);
            }

            // Nếu thay đổi nhân viên phụ trách, ghi log
            if (array_key_exists('assigned_to', $payload) && $payload['assigned_to'] !== $oldAssignedTo) {
                $assignedUser = $payload['assigned_to'] ? User::query()->find($payload['assigned_to']) : null;
                $assigneeName = $assignedUser instanceof User
                    ? $assignedUser->name
                    : ($payload['assigned_to'] ? 'Nhân viên #' . $payload['assigned_to'] : 'Chưa phân công');

                LeadNote::query()->create([
                    'lead_id' => $lead->id,
                    'created_by' => $actor->id,
                    'note' => "Cập nhật nhân viên phụ trách: {$assigneeName}.",
                ]);
            }

            return $lead->fresh([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
                'notes.createdBy:id,name',
                'appointments.handledBy:id,name',
            ]);
        });
    }

    /**
     * Chuyển nhanh giai đoạn (pipeline stage) cho Lead.
     */
    public function changeStatus(Lead $lead, string $newStatus, User $actor, ?string $note = null): Lead
    {
        if (! in_array($newStatus, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException("Trạng thái [{$newStatus}] không hợp lệ.");
        }

        if ($lead->status === $newStatus) {
            return $lead;
        }

        return DB::transaction(function () use ($lead, $newStatus, $actor, $note): Lead {
            $oldStatus = $lead->status;
            $lead->update(['status' => $newStatus]);

            $from = self::STATUS_LABELS[$oldStatus];
            $to = self::STATUS_LABELS[$newStatus];

            $logNote = "Chuyển giai đoạn: [{$from}] ➔ [{$to}].";
            if (filled($note)) {
                $logNote .= ' ' . trim((string) $note);
            }

            LeadNote::query()->create([
                'lead_id' => $lead->id,
                'created_by' => $actor->id,
                'note' => $logNote,
            ]);

            return $lead->fresh([
                'assignedTo:id,name',
                'carUnit.trim.model.make',
                'trim.model.make',
                'notes.createdBy:id,name',
                'appointments.handledBy:id,name',
            ]);
        });
    }

    /**
     * Ghi nhận ghi chú chăm sóc (activity note) cho Lead.
     */
    public function addNote(Lead $lead, string $note, User $actor): LeadNote
    {
        $cleanNote = trim($note);
        if ($cleanNote === '') {
            throw new \InvalidArgumentException('Nội dung ghi chú không được để trống.');
        }

        return LeadNote::query()->create([
            'lead_id' => $lead->id,
            'created_by' => $actor->id,
            'note' => $cleanNote,
        ]);
    }
}
