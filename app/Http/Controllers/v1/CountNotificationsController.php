<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CountNotification;

/**
 * Class CountNotificationsController
 *
 * @package App\Http\Controllers\v1
 */

class CountNotificationsController extends Controller
{
    /**
     * Get count of user notifications
     * 
     * @param $user_id
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getCountNotificationsUser($user_id){
        try{

            $countNotification = CountNotification::where('user_id' , $user_id)->get()->first();
            $countNotification = $countNotification->count;

            return $this->returnSuccess($countNotification);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Set number of notifications seen for specific user
     * 
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function currentNotificationsRead(Request $request){
        try {
            $rules = [
                'user_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if (!$validator->passes()) {
                return $this->returnBadRequest('Please fill all required fields');
            }

            $countNotification =  CountNotification::where('user_id' , $request->user_id)->get()->first();
      

            if($countNotification){
                if($request->has('count')){
                    $countNotification->count = $request->count;
                }
                $countNotification->user_id= $request->user_id;
                $countNotification->save();

                return $this->returnSuccess();
            } 

            $countNotification = new CountNotification();
            if($request->has('count')){
                $countNotification->count = $request->count;
            }
        
            $countNotification->user_id = $request->user_id;
            $countNotification->save();
            
            return $this->returnSuccess();

        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}