<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ["name", "email", "password"];
    protected $hidden = ["password"];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function teamProjects()
    {
        return $this->belongsToMany(Project::class, "team_members");
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    
}
