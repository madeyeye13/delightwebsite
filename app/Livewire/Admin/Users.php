<?php

namespace App\Livewire\Admin;

use App\Jobs\SendAccountCreatedEmail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Users extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $search = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public string $sortBy = 'newest';

    public int $perPage = 20;

    // ── Side panel ───────────────────────────────────────────────────────────
    public bool $showSidePanel = false;

    public ?int $sidePanelUserId = null;

    // ── Create / Edit modal ───────────────────────────────────────────────────
    public bool $showUserModal = false;

    public ?int $editUserId = null;

    public string $modalName = '';

    public string $modalEmail = '';

    public string $modalRole = 'customer';

    public string $modalPassword = '';

    public string $modalPasswordConfirm = '';

    public bool $modalIsActive = true;

    // ── Permissions modal ─────────────────────────────────────────────────────
    public bool $showPermissionsModal = false;

    public ?int $permissionsUserId = null;

    /** @var array<string> */
    public array $permissionsSelected = [];

    // ── Delete confirm ────────────────────────────────────────────────────────
    public bool $showDeleteConfirm = false;

    public ?int $deleteUserId = null;

    // ── Flash ─────────────────────────────────────────────────────────────────
    public string $flashMessage = '';

    public string $flashType = 'success';

    // ── Lifecycle ─────────────────────────────────────────────────────────────

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

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // ── Computed: paginated users ─────────────────────────────────────────────

    public function getUsersProperty(): LengthAwarePaginator
    {
        $query = User::query();

        if ($this->search !== '') {
            $query->where(function ($q): void {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->roleFilter !== '') {
            $query->where('role', $this->roleFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        match ($this->sortBy) {
            'oldest' => $query->oldest(),
            'name-asc' => $query->orderBy('name'),
            'name-desc' => $query->orderByDesc('name'),
            default => $query->latest(),
        };

        return $query->paginate($this->perPage);
    }

    /** @return array<string, int> */
    public function getStatsProperty(): array
    {
        return [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'customer' => User::where('role', 'customer')->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];
    }

    /** @return array<string, mixed>|null */
    public function getSidePanelDataProperty(): ?array
    {
        if (! $this->sidePanelUserId) {
            return null;
        }

        $user = User::find($this->sidePanelUserId);
        if (! $user) {
            return null;
        }

        $orderCount = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('total');
        $lastOrder = Order::where('user_id', $user->id)->latest()->first();

        return [
            'user' => $user,
            'order_count' => $orderCount,
            'total_spent' => $totalSpent,
            'last_order' => $lastOrder?->created_at?->diffForHumans() ?? 'Never',
        ];
    }

    // ── Side panel ────────────────────────────────────────────────────────────

    public function openSidePanel(int $userId): void
    {
        $this->sidePanelUserId = $userId;
        $this->showSidePanel = true;
    }

    public function closeSidePanel(): void
    {
        $this->showSidePanel = false;
        $this->sidePanelUserId = null;
    }

    // ── Create / Edit modal ───────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetModalFields();
        $this->editUserId = null;
        $this->showUserModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->resetModalFields();
        $this->editUserId = $userId;
        $this->modalName = $user->name;
        $this->modalEmail = $user->email;
        $this->modalRole = $user->role ?? 'customer';
        $this->modalIsActive = $user->is_active;
        $this->modalPassword = '';
        $this->modalPasswordConfirm = '';
        $this->showUserModal = true;
    }

    public function saveUser(): void
    {
        $rules = [
            'modalName' => ['required', 'string', 'max:255'],
            'modalEmail' => ['required', 'email', 'max:255'],
            'modalRole' => ['required', 'in:admin,staff,customer'],
        ];

        if ($this->editUserId) {
            $rules['modalEmail'][] = 'unique:users,email,'.$this->editUserId;
            if ($this->modalPassword !== '') {
                $rules['modalPassword'] = ['required', 'min:8'];
                $rules['modalPasswordConfirm'] = ['required', 'same:modalPassword'];
            }
        } else {
            $rules['modalEmail'][] = 'unique:users,email';
            $rules['modalPassword'] = ['required', 'min:8'];
            $rules['modalPasswordConfirm'] = ['required', 'same:modalPassword'];
        }

        $this->validate($rules, [
            'modalName.required' => 'Name is required.',
            'modalEmail.required' => 'Email is required.',
            'modalEmail.unique' => 'That email is already taken.',
            'modalRole.required' => 'Role is required.',
            'modalPassword.required' => 'Password is required.',
            'modalPassword.min' => 'Password must be at least 8 characters.',
            'modalPasswordConfirm.required' => 'Please confirm the password.',
            'modalPasswordConfirm.same' => 'Passwords do not match.',
        ]);

        $data = [
            'name' => $this->modalName,
            'email' => $this->modalEmail,
            'role' => $this->modalRole,
            'is_active' => $this->modalIsActive,
        ];

        if ($this->modalPassword !== '') {
            $data['password'] = Hash::make($this->modalPassword);
        }

        if ($this->editUserId) {
            $user = User::findOrFail($this->editUserId);
            $user->update($data);
            $this->flash('User updated successfully.', 'success');
        } else {
            $plainPassword = $this->modalPassword;
            $user = User::create($data);
            SendAccountCreatedEmail::dispatch($user, $plainPassword);
            $this->flash('User created successfully.', 'success');
        }

        $this->showUserModal = false;
        $this->dispatch('close-user-modal');
        $this->resetModalFields();
    }

    // ── Toggle active status ──────────────────────────────────────────────────

    public function toggleActive(int $userId): void
    {
        if ($userId === auth()->id()) {
            $this->flash('You cannot deactivate your own account.', 'error');

            return;
        }

        $user = User::findOrFail($userId);
        $user->is_active = ! $user->is_active;
        $user->save();

        $label = $user->is_active ? 'activated' : 'deactivated';
        $this->flash('User '.$label.'.', 'success');
    }

    // ── Permissions modal ─────────────────────────────────────────────────────

    public function openPermissionsModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->permissionsUserId = $userId;
        $this->permissionsSelected = $user->permissions ?? [];
        $this->showPermissionsModal = true;
        $this->dispatch('open-permissions-modal');
    }

    public function savePermissions(): void
    {
        $user = User::findOrFail($this->permissionsUserId);
        $user->permissions = array_values($this->permissionsSelected);
        $user->save();

        $this->showPermissionsModal = false;
        $this->dispatch('close-permissions-modal');
        $this->flash('Permissions updated.', 'success');
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function confirmDelete(int $userId): void
    {
        if ($userId === auth()->id()) {
            $this->flash('You cannot delete your own account.', 'error');

            return;
        }

        $this->deleteUserId = $userId;
        $this->showDeleteConfirm = true;
        $this->dispatch('open-delete-confirm');
    }

    public function deleteUser(): void
    {
        if ($this->deleteUserId === auth()->id()) {
            $this->flash('You cannot delete your own account.', 'error');
            $this->showDeleteConfirm = false;

            return;
        }

        $user = User::find($this->deleteUserId);
        if ($user) {
            $user->delete();
        }

        $this->showDeleteConfirm = false;
        $this->dispatch('close-delete-confirm');
        $this->deleteUserId = null;

        if ($this->showSidePanel && $this->sidePanelUserId === $this->deleteUserId) {
            $this->closeSidePanel();
        }

        $this->flash('User deleted.', 'success');
    }

    // ── Misc ──────────────────────────────────────────────────────────────────

    public function clearFilters(): void
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->sortBy = 'newest';
        $this->resetPage();
    }

    public function exportUsers(): StreamedResponse
    {
        $users = User::query()
            ->when($this->search !== '', function ($q): void {
                $q->where(function ($q): void {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->roleFilter !== '', fn ($q) => $q->where('role', $this->roleFilter))
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($users): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Status', 'Joined']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 'users-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resetModalFields(): void
    {
        $this->modalName = '';
        $this->modalEmail = '';
        $this->modalRole = 'customer';
        $this->modalPassword = '';
        $this->modalPasswordConfirm = '';
        $this->modalIsActive = true;
        $this->resetValidation();
    }

    private function flash(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', message: $message, type: $type);
    }

    public function render(): View
    {
        return view('livewire.admin.users', [
            'users' => $this->users,
            'stats' => $this->stats,
            'adminPages' => User::adminPages(),
        ]);
    }
}
