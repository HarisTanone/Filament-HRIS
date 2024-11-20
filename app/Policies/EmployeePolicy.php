<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['view employees', 'view own employee']);
    }

    public function view(User $user, Employee $employee)
    {
        if ($user->hasPermissionTo('view employees')) {
            return true;
        }

        return $user->hasPermissionTo('view own employee') && $employee->user_id === $user->id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create employees');
    }

    public function update(User $user, Employee $employee)
    {
        return $user->hasPermissionTo('edit employees');
    }

    public function delete(User $user, Employee $employee)
    {
        return $user->hasPermissionTo('delete employees');
    }
}
