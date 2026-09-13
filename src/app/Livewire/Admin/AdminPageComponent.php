<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Support\Admin\AdminContextResolver;
use Livewire\Component;

abstract class AdminPageComponent extends Component
{
    public function boot(): void
    {
        $this->authorizeAdminAccess($this->requiredPermission());
        $this->dispatchFlashedToast();
    }

    abstract protected function requiredPermission(): ?string;

    protected function authorizeAdminAccess(?string $permission = null): User
    {
        $user = auth()->user();

        if (! $user instanceof User || ! $user->hasAnyRole(['admin', 'staff'])) {
            abort(403, 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        if ($permission !== null && ! $user->hasPermission($permission)) {
            abort(403, 'Bạn không đủ quyền thực hiện thao tác này.');
        }

        return $user;
    }

    protected function toast(string $type, string $message): void
    {
        $this->dispatch('toast', type: $type, message: $message);
    }

    protected function flashToast(string $type, string $message): void
    {
        session()->flash('admin_toast', [
            'type' => $type,
            'message' => $message,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function adminLayoutData(array $data = []): array
    {
        return array_merge(app(AdminContextResolver::class)->resolve(), $data);
    }

    private function dispatchFlashedToast(): void
    {
        $toast = session()->pull('admin_toast');

        if (! is_array($toast)) {
            return;
        }

        $message = trim((string) ($toast['message'] ?? ''));

        if ($message === '') {
            return;
        }

        $this->toast((string) ($toast['type'] ?? 'success'), $message);
    }
}
