<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Role;
use App\Task;
use App\Comment;

/**
 * Class CommentController
 *
 * @package App\Http\Controllers\v1
 */

class CommentController extends Controller{

    /**
     * Get comment list
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAll() {
        try {
            $comments = Comment::all();

            return $this->returnSuccess($comments);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Create a comment
     *
     * @param Request $request
     * 
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function create($id,Request $request) {
        try {
            $rules = [
                'comment'     => 'required|min:1'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ( ! $validator->passes()) {
                return $this->returnBadRequest();
            }
            $task = Task::find($id);

            $comment = new Comment([
                'user_id' => $task->user_id,
                'task_id' => $id,
                'comment' =>$request->comment
            ]);

            if($comment->save())
            {
                return $this->returnSuccess($comment);
            }

            return $this->returnError('An error occured');

        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

   
}