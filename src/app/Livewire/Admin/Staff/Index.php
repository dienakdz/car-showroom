<?php

namespace App\Livewire\Admin\Staff;

use App\Livewire\Admin\AdminPageComponent;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\StaffManagementService;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;

class Index extends AdminPageComponent
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public bool $showFormModal = false;

    #[Locked]
    public ?int $editingStaffId = null;

    /** @var array<string, mixed> */
    public array $form = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'password' => '',
        'role_id' => 0,
        'is_active' => true,
    ];

    public bool $showRoleModal = false;

    #[Locked]
    public ?int $selectedRoleId = null;

    /** @var array<int, int> */
    public array $selectedRolePermissions = [];

    /** @var array{type?: string, message?: string} */
    public array $feedback = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function openCreateModal(StaffManagementService $service): void
    {
        $this->resetErrorBag();
        $this->editingStaffId = null;

        $staffRole = $service->getAllRoles()->firstWhere('name', 'staff');
        $firstRole = $service->getAllRoles()->first();
        $defaultRoleId = $staffRole ? (int) $staffRole->id : ($firstRole ? (int) $firstRole->id : 0);

        $this->form = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'password' => '',
            'role_id' => $defaultRoleId,
            'is_active' => true,
        ];

        $this->showFormModal = true;
    }

    public function openEditModal(int $staffId, StaffManagementService $service): void
    {
        $this->resetErrorBag();
        $staff = User::with('roles')->findOrFail($staffId);

        $this->editingStaffId = $staff->id;
        $userRole = $staff->roles->first();
        $firstRole = $service->getAllRoles()->first();
        $currentRoleId = $userRole ? (int) $userRole->id : ($firstRole ? (int) $firstRole->id : 0);

        $this->form = [
            'name' => $staff->name,
            'email' => $staff->email,
            'phone' => $staff->phone ?? '',
            'password' => '',
            'role_id' => $currentRoleId,
            'is_active' => $staff->is_active,
        ];

        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->editingStaffId = null;
        $this->resetErrorBag();
    }

    public function saveStaff(StaffManagementService $service): void
    {
        $this->resetErrorBag();

        $rules = [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => [
                'required',
                'email',
                'max:255',
                $this->editingStaffId !== null
                    ? Rule::unique('users', 'email')->ignore($this->editingStaffId)
                    : Rule::unique('users', 'email'),
            ],
            'form.phone' => ['nullable', 'string', 'max:20'],
            'form.role_id' => ['required', 'integer', 'exists:roles,id'],
            'form.is_active' => ['boolean'],
        ];

        if ($this->editingStaffId === null) {
            $rules['form.password'] = ['required', 'string', 'min:6'];
        } else {
            $rules['form.password'] = ['nullable', 'string', 'min:6'];
        }

        $validated = $this->validate($rules, attributes: [
            'form.name' => 'Họ và tên',
            'form.email' => 'Địa chỉ Email',
            'form.phone' => 'Số điện thoại',
            'form.password' => 'Mật khẩu',
            'form.role_id' => 'Vai trò',
            'form.is_active' => 'Trạng thái hoạt động',
        ]);

        /** @var array{name: string, email: string, phone?: ?string, password?: ?string, role_id: int, is_active?: bool} $formData */
        $formData = $validated['form'];

        try {
            $actor = auth()->user();
            if ($this->editingStaffId === null) {
                /** @var array{name: string, email: string, phone?: ?string, password: string, role_id: int, is_active?: bool} $createData */
                $createData = $formData;
                $service->createStaff($createData, $actor instanceof User ? $actor : null);
                $this->toast('success', 'Đã thêm tài khoản nhân viên mới thành công.');
            } else {
                $staff = User::findOrFail($this->editingStaffId);
                $service->updateStaff($staff, $formData, $actor instanceof User ? $actor : null);
                $this->toast('success', 'Đã cập nhật thông tin nhân viên thành công.');
            }

            $this->closeFormModal();
        } catch (Exception $e) {
            $this->addError('form.general', $e->getMessage());
        }
    }

    public function toggleStaffStatus(int $staffId, StaffManagementService $service): void
    {
        try {
            $actor = auth()->user();
            if (! $actor instanceof User) {
                return;
            }

            $staff = User::findOrFail($staffId);
            $newStatus = $service->toggleStatus($staff, $actor);

            $this->toast(
                $newStatus ? 'success' : 'info',
                $newStatus
                    ? "Đã mở khóa tài khoản của {$staff->name}."
                    : "Đã tạm khóa tài khoản của {$staff->name}."
            );
        } catch (Exception $e) {
            $this->toast('error', $e->getMessage());
        }
    }

    public function deleteStaff(int $staffId, StaffManagementService $service): void
    {
        try {
            $actor = auth()->user();
            if (! $actor instanceof User) {
                return;
            }

            $staff = User::findOrFail($staffId);
            $name = $staff->name;
            $service->deleteStaff($staff, $actor);

            $this->toast('success', "Đã xóa vĩnh viễn tài khoản nhân viên {$name}.");
        } catch (Exception $e) {
            $this->toast('error', $e->getMessage());
        }
    }

    public function openRoleModal(int $roleId, StaffManagementService $service): void
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $this->selectedRoleId = $role->id;
        $this->selectedRolePermissions = $role->permissions->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->showRoleModal = true;
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->selectedRoleId = null;
        $this->selectedRolePermissions = [];
    }

    public function saveRolePermissions(StaffManagementService $service): void
    {
        if ($this->selectedRoleId === null) {
            return;
        }

        try {
            $intPermissionIds = array_values(array_map('intval', $this->selectedRolePermissions));
            $service->updateRolePermissions($this->selectedRoleId, $intPermissionIds);
            $this->toast('success', 'Đã cập nhật phân quyền cho vai trò thành công.');
            $this->closeRoleModal();
        } catch (Exception $e) {
            $this->toast('error', $e->getMessage());
        }
    }

    public function dismissFeedback(): void
    {
        $this->feedback = [];
    }

    public function render(StaffManagementService $service): View
    {
        $filters = [
            'search' => $this->search,
            'role' => $this->roleFilter,
            'status' => $this->statusFilter,
        ];

        return view('livewire.admin.staff.index', [
            'stats' => $service->getStats(),
            'staffList' => $service->getPaginatedStaff($filters, 10),
            'roles' => $service->getAllRoles(),
            'permissions' => $service->getAllPermissions(),
            'selectedRole' => $this->selectedRoleId ? Role::find($this->selectedRoleId) : null,
        ])->layout('admin.layouts.livewire', $this->adminLayoutData([
            'adminPageTitle' => 'Quản lý Nhân viên & Phân quyền',
            'adminPageDescription' => 'Quản lý tài khoản nhân viên showroom, gán vai trò và phân quyền hạn hệ thống.',
        ]));
    }

    protected function requiredPermission(): ?string
    {
        return 'users.manage';
    }
}
