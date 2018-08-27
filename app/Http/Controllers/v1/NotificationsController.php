<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class NotificationsController
 *
 * @package App\Http\Controllers\v1
 */

class NotificationsController extends Controller
{
    /**
     * Get notifications list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll() {
        try {
            $user = $this->validateSession();
            $notifications = Notification::where('user_id',$user->id)->get();
           
            return $this->returnSuccess($notifications);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

}