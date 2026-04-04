<?php

namespace App\Services\Admin;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AppointmentManagementService
{
    public function save(array $validated, User $actor, ?Appointment $appointment = null): Appointment
    {
        return DB::transaction(function () use ($validated, $actor, $appointment): Appointment {
            $appointment ??= new Appointment();

            $lead = null;

            if (! empty($validated['lead_id'])) {
                $lead = Lead::query()->find($validated['lead_id']);
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
                $payload['user_id'] = $payload['user_id'] ?? $lead->user_id;
                $payload['car_unit_id'] = $payload['car_unit_id'] ?? $lead->car_unit_id;
                $payload['trim_id'] = $payload['trim_id'] ?? $lead->trim_id;
            }

            $payload['handled_by'] = $payload['handled_by'] ?? $actor->id;

            $appointment->fill($payload);
            $appointment->save();

            if ($lead !== null && $appointment->status !== 'cancelled' && $lead->status !== 'closed') {
                $lead->update(['status' => 'booked']);
            }

            return $appointment->fresh([
                'user',
                'carUnit.trim.model.make',
                'trim.model.make',
                'lead',
                'handledBy',
            ]);
        });
    }
}
