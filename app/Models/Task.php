<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "title",
        "description",
        "due_date",
        "priority",
        "status",
        "project_id",
        "assigned_to",
        "estimated_hours",
        "spent_hours",
    ];

    protected $casts = [
        "due_date" => "date",
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
