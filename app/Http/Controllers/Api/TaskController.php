<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project, Request $request): AnonymousResourceCollection
    {
        $this->authorize("view", $project);

        $query = $project->tasks();

        if ($request->status) {
            $query->where("status", $request->status);
        }

        if ($request->priority) {
            $query->where("priority", $request->priority);
        }

        $tasks = $query->with("assignedUser")->paginate(15);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project): TaskResource
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:today', // Não pode ser data passada
            'priority' => 'required|in:baixa,média,alta',
            'estimated_hours' => 'nullable|integer|min:1',
            'assigned_to' => 'nullable|exists:users,id', // Usuário deve existir
        ]);

        $task = $project->tasks()->create($validated);

        return new TaskResource($task->load('assignedUser'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);
        return new TaskResource($task->load('assignedUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pendente,em_andamento,concluída',
            'priority' => 'in:baixa,média,alta',
            'spent_hours' => 'nullable|integer|min:0',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return new TaskResource($task->load('assignedUser'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(['message' => 'Tarefa deletada com sucesso']);
    }
}
