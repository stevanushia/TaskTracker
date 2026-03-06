<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::withCount('tasks')->orderBy('created_at', 'desc');

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by Name
        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%'); // 'ilike' for case-insensitive in PostgreSQL
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        // 1. Validate incoming request (now including status)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Allow the status to be set on creation, defaulting to 'active' if missing
            'status' => ['nullable', Rule::in(['active', 'archived'])],
        ], [
            'name.required' => 'Nama project wajib diisi.'
        ]);

        // 2. Determine the final status (safe default)
        $status = $validated['status'] ?? 'active';

        // 3. Create the project using the status from the frontend
        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $status, // 👈 Use the provided status
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Project berhasil ditambahkan', 'data' => $project], 201);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'archived'])],
        ], [
            'name.required' => 'Nama project wajib diisi.'
        ]);

        $project->update($validated);

        return response()->json(['message' => 'Project berhasil diupdate', 'data' => $project]);
    }

    public function show($id)
    {
        $project = Project::with(['tasks' => function($query) {
            $query->orderBy('created_at', 'desc'); // Newest tasks first
        }])->findOrFail($id);

        $categories = Category::orderBy('id', 'asc')->get();
        $doneCategory = $categories->where('name', 'Done')->first();

        // Calculate stats for the header
        $totalTasks = $project->tasks->count();
        $completedTasks = $doneCategory ? $project->tasks->where('category_id', $doneCategory->id)->count() : 0;

        return response()->json([
            'project' => $project,
            'categories' => $categories,
            'stats' => [
                'total' => $totalTasks,
                'completed' => $completedTasks
            ]
        ]);
    }
}
