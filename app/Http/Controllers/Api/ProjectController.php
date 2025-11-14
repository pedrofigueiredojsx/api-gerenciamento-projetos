<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Project::where(function ($q) use ($request) {
            $q->where("user_id", $request->user()->id)
                ->orWhereHas("teamMembers", function ($q2) {
                    $q2->where("user_id", auth()->id());
                });
        });

        if ($request->has('status') && $request->status !== 'all') {
            $status = strtolower($request->status);

            if ($status === 'concluido') {
                $status = 'concluído';
            }

            $query->whereRaw('LOWER(status) LIKE ?', [$status . '%']);
        }

        $projects = $query
            ->with(["owner", "teamMembers", "tasks"])
            ->paginate(10);

        return ProjectResource::collection($projects);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): ProjectResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'nullable|integer|min:0',
            'status' => 'required|in:planejamento,em_progresso,concluído,pausado'
        ]);

        $project = $request->user()->projects()->create($validated);

        return new ProjectResource($project->load(['owner', "teamMembers", "tasks"]));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project): ProjectResource
    {
        $this->authorize("view", $project);

        return new ProjectResource($project->load(["owner", "teamMembers", "tasks"]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project): ProjectResource
    {
        $this->authorize("update", $project);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:planejamento,em_progresso,concluído,pausado',
            'budget' => 'nullable|integer|min:0',
        ]);

        $project->update($validated);

        return new ProjectResource($project->load(['owner', 'teamMembers', 'tasks']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize("delete", $project);

        $project->delete();

        return response()->json(["message" => "Projeto deletado com sucesso"]);
    }

    public function stats(Project $project): JsonResponse
    {
        $this->authorize("view", $project);

        return response()->json([
            "project_name" => $project->name,
            "total_tasks" => $project->tasks()->count(),
            "completed_tasks" => $project->tasks()->where("status", "concluída")->count(),
            "pending_tasks" => $project->tasks()->where("status", "pendente")->count(),
            "in_progress_tasks" => $project->tasks()->where("status", "em_andamento")->count(),
            "high_priority_tasks" => $project->tasks()->where("priority", "alta")->count(),
            "progress" => $project->progress,
            "team_members" => $project->teamMembers()->count() + 1,
        ]);
    }
}
