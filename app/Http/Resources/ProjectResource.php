<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            "name" => $this->name,
            "description" => $this->description,
            "status" => $this->status,
            "progress" => $this->progress,
            "budget" => $this->budget,
            "start_date" => $this->start_date?->format("Y-m-d"),
            "end_date" => $this->end_date?->format("Y-m-d"),
            "team_count" => $this->team_count,
            "owner" => new UserResource($this->owner),
            "tasks_count" => $this->tasks()->count(),
            "completed_tasks" => $this->tasks()->where("status", "concluida")->count(),
            "created_at" => $this->created_at->toIso8601String(),
        ];
    }
}
