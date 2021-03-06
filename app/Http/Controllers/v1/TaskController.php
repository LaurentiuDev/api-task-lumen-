<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Role;
use App\Task;
use App\Notification;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;

/**
 * Class TaskController
 *
 * @package App\Http\Controllers\v1
 */
class TaskController extends Controller
{
    /**
     * Get tasks list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $user = $this->validateSession();

            if ($user->role_id === Role::ROLE_USER) {
                $tasks = Task::where('assign', $user->id)->paginate(10);
            } else {
                $tasks = Task::paginate(10);
            }

            return $this->returnSuccess($tasks);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Create a task
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $user = $this->validateSession();

            $rules = [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required',
                'assign' => 'required|exists:users,id'
            ];

            $validator = Validator::make($request->all(), $rules);

            if (!$validator->passes()) {
                return $this->returnBadRequest('Please fill all required fields');
            }

            $task = new Task();

            $task->name = $request->name;
            $task->description = $request->description;
            $task->status = $request->status ? $request->status : Task::STATUS_ASSIGNED;
            $task->user_id = $user->id;
            $task->assign = $request->assign;

            $notification = new Notification();
            $notification->user_id = $task->assign;
            $notification->message = $user->name . ' has assigned you a task';
            $notification->save();


            $task->save();

            return $this->returnSuccess();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Update a task
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $this->validateSession();

            $task = Task::find($id);

            if ($user->role_id === Role::ROLE_USER && $user->id !== $task->assign) {
                return $this->returnError('You don\'t have permission to update this task');
            }
            $old_status = $task->status;
            $old_assign = $task->assign;
            $status = '';
            switch ($task->status) {
                case Task::STATUS_ASSIGNED:
                    $status = 'Assigned';
                    break;
                case Task::STATUS_IN_PROGRESS:
                    $status = 'In progress';
                    break;
                case Task::STATUS_NOT_DONE:
                    $status = 'Not done';
                    break;
                case Task::STATUS_DONE:
                    $status = 'Done';
                    break;
            }

            $users = User::where('id', $task->assign)->get()->first();

            $log = new Log([
                'task_id' => $id,
                'user_id' => $task->user_id,
                'old_value' => 'Status : ' . $status . ' . Assign to ' . $users->name
            ]);

            if ($request->has('name')) {
                $task->name = $request->name;
            }

            if ($request->has('description')) {
                $task->description = $request->description;
            }

            if ($request->has('status')) {
                if ($old_status !== $request->status) {
                    $log->type = Log::STATUS;
                }
                $task->status = $request->status;

            }

            if ($request->has('assign')) {
                if ($old_assign !== $request->assign) {
                    $log->type = Log::ASSIGN;
                }

                $task->assign = $request->assign;

                $notification = new Notification();
                $notification->user_id = $task->assign;
                $notification->message = $user->name . ' has assigned you a task';
                $notification->save();
            }

            $status = '';
            switch ($task->status) {
                case Task::STATUS_ASSIGNED:
                    $status = 'Assigned';
                    break;
                case Task::STATUS_IN_PROGRESS:
                    $status = 'In progress';
                    break;
                case Task::STATUS_NOT_DONE:
                    $status = 'Not done';
                    break;
                case Task::STATUS_DONE:
                    $status = 'Done';
                    break;
            }
            $users = User::where('id', $task->assign)->get()->first();
            $log->new_value = 'Status : ' . $status . ' . Assign to ' . $users->name;
            $log->save();

            $task->save();

            return $this->returnSuccess();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Delete a task
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            $user = $this->validateSession();

            if ($user->role_id !== Role::ROLE_ADMIN) {
                return $this->returnError('You don\'t have permission to delete this task');
            }

            $task = Task::find($id);

            $task->delete();

            return $this->returnSuccess();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}