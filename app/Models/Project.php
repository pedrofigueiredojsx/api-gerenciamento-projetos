<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'budget',
        'user_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['progress', 'team_count'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teamMembers()
    {
        return $this->belongsToMany((User::class))->withPivot('role');
    }

    public function getProgressAttribute()
    {
        $total = $this->tasks()->count();

        if ($total == 0)
            return 0;

        $completed = $this->tasks()->where('status', 'concluida')->count();

        return round(($completed / $total) * 100);
    }

    public function getTeamCountAttribute()
    {
        return $this->teamMembers()->count() + 1;
    }
}
