<?php
namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Authorize
{
    public static function isSuperAdmin($user): bool
    {
        return in_array($user->role, ['super_admin']);
    }

    public static function hasPermission($user, string $permission, ?int $libraryId = null, bool $onlyAdminAndSuperAdmin = false): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        //check role (optional)
        if ($onlyAdminAndSuperAdmin && !in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Forbidden.');
        }

        //library restriction
        if ($libraryId !== null && $user->library_id !== $libraryId) {
            abort(403, 'Forbidden.');
        }

        //check privilege
        if (!$user->privilages()->where('privilage_name', $permission)->exists()) {
            abort(403, 'Forbidden.');
        }


        return true;
    }

    public static function canUpdateUser($user, User $targetUser): bool
    {
        //super Admin can update anything
        if ($user->role === 'super_admin') {
            return true;
        }

        //admin can update only staff within their library
        if (
            $user->role === 'admin' &&
            $targetUser->role === 'staff' &&
            $targetUser->library_id === $user->library_id
        ) {
            return true;
        }

        abort(403, 'Forbidden.');
    }
}

