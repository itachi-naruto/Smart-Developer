<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        $this->roles()->detach($role);
    }

   // app/Models/User.php

public function hasPermissionTo($permission, $resource = null)
{
    foreach ($this->roles as $role) {
        if ($role->permissions->contains('name', $permission)) {
            
            if (is_null($resource)) {
                return true;
            }

            // Check if the user owns the resource
            if ($this->owns($resource)) {
                return true;
            }

            if ($this->canAccessBasedOnHierarchy($resource)) {
                return true;
            }
        }
    }

    return false;
}

    public function canAccessBasedOnHierarchy($resource)
    {
        // Example: Check if the user is a manager and belongs to the same department as the resource's owner
        return $this->isManager() && $this->department_id === $resource->user->department_id;
    }

    public function isManager()
    {
        return $this->roles()->where('name', 'manager')->exists();
    }

    public function owns($resource)
    {
        return $this->id === $resource->user_id;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
