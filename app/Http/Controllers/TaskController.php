<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['created_by'] = $request->user()->id;
        $task = Task::create($validated);

        return response()->json(['message' => 'Task berhasil dibuat', 'data' => $task], 201);
    }

    public function update(Request $request, Task $task)
    {
        // This handles both full edits and simple drag-and-drop category updates
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $task->update($validated);

        return response()->json(['message' => 'Task berhasil diupdate', 'data' => $task]);
    }

    public function destroy(Request $request, Task $task)
    {
        // Requirement: fill deleted_by before soft deleting
        $task->update(['deleted_by' => $request->user()->id]);
        $task->delete(); // Triggers soft delete (deleted_at)

        return response()->json(['message' => 'Task berhasil dihapus']);
    }
}
