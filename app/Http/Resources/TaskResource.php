<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "status" => $this->status,
            "priority" => $this->priority,
            "due_date" => $this->due_date->format("Y-m-d"),
            "estimated_hours" => $this->estimated_hours,
            "spent_hours" => $this->spent_hours,

            // Calcular percentual de horas gastas
            "progress" => $this->spent_hours
                ? round(($this->spent_hours / $this->estimated_hours) * 100)
                : 0,

            "project_id" => $this->project_id,
            "assigned_to" => $this->assigned_to,
            "assigned_user" => new UserResource($this->assignedUser),
            "created_at" => $this->created_at->toIso8601String(),
        ];
    }
}
