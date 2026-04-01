<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tasks = auth()->user()->tasks();
            if ($request->status) {
                $tasks->where('status', $request->status);
            }
            $paginated = $tasks->latest()->paginate(10);

            return response()->json([
                'message' => 'List of tasks',
                'tasks' => $paginated->items(),
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'total_pages' => $paginated->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching tasks'], 500);
        }
    }

    public function create(TaskRequest $request)
    {
        try {
            $task = auth()->user()->tasks()->create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'due_date' => Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d'),
            ]);

            return response()->json(['success' => true, 'message' => 'Task created successfully', 'task' => $task], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while creating the task', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::where('id', $id)->where('user_id', auth()->id())->first();
            if (!$task) {
                return response()->json(['success' => false, 'message' => 'Task not found'], 404);
            }
            return response()->json(['success'  => true, 'message' => "Details of task with id: $id", 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching tasks', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = Task::where('id', $id)->where('user_id', auth()->id())->first();
            if (!$task) {
                return response()->json(['success' => false, 'message' => 'Task not found'], 404);
            }

            $data = $request->only(['title', 'description', 'status', 'due_date']);
            
            if (!empty($data['due_date'])) {
                $data['due_date'] = Carbon::createFromFormat('d-m-Y', $data['due_date'])->format('Y-m-d');
            }
            $task->update($data);

            return response()->json(['success' => true, 'message' => "Task with id: $id updated successfully", 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while updating the task', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::where('id', $id)->where('user_id', auth()->id())->first();
            if (!$task) {
                return response()->json(['success' => false, 'message' => 'Task not found'], 404);
            }
            $task->delete();

            return response()->json(['success' => true, 'message' => "Task with id: $id deleted successfully"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the task'], 500);
        }
    }

    public function markCompleted($id)
    {
        try {
            $task = Task::where('id', $id)->where('user_id', auth()->id())->first();
            if (!$task) {
                return response()->json(['success' => false, 'message' => 'Task not found'], 404);
            }

            $task->update(['status' => 'completed']);

            return response()->json(['success' => true, 'message' => "Task with id: $id marked as completed", 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while marking the task as completed'], 500);
        }
    }
}
