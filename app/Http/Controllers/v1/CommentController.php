<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Role;
use App\Task;
use App\Comment;

class CommentController extends Controller{

    public function getAll() {
        try {
            $comments = Comment::all();

            return $this->returnSuccess($comments);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

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