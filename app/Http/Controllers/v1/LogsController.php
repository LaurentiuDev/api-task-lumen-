<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use App\Log;

class LogsController extends Controller {
    public function getAll (){
        try {
            $logs = Log::paginate(10);

            return $this->returnSuccess($logs);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}