<?php

namespace App\Http\Controllers\Clients;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AppointmentController extends ClientBaseController
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source' => ['required', Rule::in(self::LEAD_SOURCES)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+()\\-\\s.]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string'],
            'car_unit_id' => ['nullable', 'integer', 'exists:car_units,id'],
            'trim_id' => ['nullable', 'integer', 'exists:trims,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        if (($validated['car_unit_id'] ?? null) === null && ($validated['trim_id'] ?? null) === null) {
            return back()
                ->withErrors(['scheduled_at' => 'Cần xác định mẫu xe hoặc phiên bản trước khi đặt lịch trải nghiệm.'])
                ->withInput();
        }

        $appointment = DB::transaction(function () use ($validated): Appointment {
            $lead = $this->createLead([
                'source' => $validated['source'],
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'message' => $validated['message'] ?? null,
                'car_unit_id' => $validated['car_unit_id'] ?? null,
                'trim_id' => $validated['trim_id'] ?? null,
                'status' => 'booked',
            ]);

            return Appointment::query()->create([
                'user_id' => auth()->id(),
                'car_unit_id' => $validated['car_unit_id'] ?? null,
                'trim_id' => $validated['trim_id'] ?? null,
                'lead_id' => $lead->id,
                'handled_by' => $lead->assigned_to,
                'scheduled_at' => $validated['scheduled_at'],
                'status' => 'pending',
                'note' => $validated['message'] ?? null,
            ]);
        });

        try {
            $timeStr = Carbon::parse($validated['scheduled_at'])->format('H:i d/m/Y');
            app(\App\Services\Admin\NotificationService::class)->notifyAdmins(
                'appointment',
                'Lịch hẹn lái thử mới từ Website',
                "Khách hàng {$validated['name']} ({$validated['phone']}) đã đặt lịch hẹn lúc {$timeStr}.",
                route('admin.appointments.index'),
                'fa fa-calendar-check',
                ['appointment_id' => $appointment->id, 'customer_name' => $validated['name']]
            );

            $currentUser = auth()->user();
            if ($currentUser instanceof User && ! $currentUser->hasAnyRole(['admin', 'staff'])) {
                app(\App\Services\Admin\NotificationService::class)->notifyUser(
                    $currentUser,
                    'appointment',
                    'Xác nhận tiếp nhận lịch hẹn lái thử',
                    "Lịch hẹn trải nghiệm xe của bạn lúc {$timeStr} đã được showroom tiếp nhận.",
                    route('account.show', ['tab' => 'appointments']),
                    'fa-solid fa-calendar-check',
                    ['appointment_id' => $appointment->id]
                );
            }
        } catch (\Throwable) {
        }

        $successMessage = 'Yêu cầu đặt lịch lái thử đã được ghi nhận. Showroom sẽ sớm liên hệ xác nhận.';
        $this->pushSuccessToast($successMessage);

        return back();
    }
}
