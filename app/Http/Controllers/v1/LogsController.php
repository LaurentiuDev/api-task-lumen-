<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use App\Log;

/**
 * Class LogsController
 *
 * @package App\Http\Controllers\v1
 */

class LogsController extends Controller {

    /**
     * Get logs list
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAll (){
        try {
            
            $logs = Log::paginate(10);

            return $this->returnSuccess($logs);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}