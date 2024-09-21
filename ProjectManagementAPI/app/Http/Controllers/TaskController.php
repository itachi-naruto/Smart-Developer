<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    public function store(Request $request, $projectId)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:In Progress,Completed,Deferred',
        ]);

        $task = Task::create($request->all() + ['project_id' => $projectId]);

        return response()->json($task, 201);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => 'in:In Progress,Completed,Deferred']);

        $task->update($request->only('status'));

        return response()->json($task);
    }

    public function assignUser(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $task = Task::findOrFail($id);
        $previousOwner = $task->user_id;
        $newOwner = $request->user_id;
        
        $task->update([
            'user_id' => $newOwner
        ]);

        if ($previousOwner != $newOwner) {
            // Send notifications to the new owner and (optionally) the previous owner
            $this->notifyUser($newOwner, $task);
        }

        return response()->json([
            'message' => 'Task assigned successfully',
            'task' => $task
        ]);
    }

    protected function notifyUser($userId, $task)
    {
        // Fetch the user to notify
        $user = User::find($userId);

        if ($user) {
            $user->notify(new TaskAssigned($task)); // Trigger notification
        }
    }
}
