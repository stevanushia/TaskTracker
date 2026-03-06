<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Active Projects
        $activeProjects = Project::where('status', 'active')->count();

        // 2. Total Incomplete Tasks (Assuming 'Done' category means complete)
        $doneCategory = Category::where('name', 'Done')->first();
        $incompleteTasks = Task::where('category_id', '!=', $doneCategory?->id ?? 0)->count();

        // 3. Tasks approaching due date (excluding done tasks)
        $upcomingTasks = Task::with('project')
            ->where('category_id', '!=', $doneCategory?->id ?? 0)
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->take(5) // Limit to 5 tasks for the dashboard
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'project_name' => $task->project->name ?? 'No Project',
                    'due_date' => $task->due_date,
                ];
            });

        return response()->json([
            'active_projects' => $activeProjects,
            'incomplete_tasks' => $incompleteTasks,
            'upcoming_tasks' => $upcomingTasks
        ]);
    }
}
