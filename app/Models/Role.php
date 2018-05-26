<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ID_ADMIN = 1;

    protected $fillable = [
        'name',
        'description',
    ];

    public function perms()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    //给角色添加权限
    public function givePermissionTo($permission)
    {
        return $this->permissions()->save($permission);
    }
}
