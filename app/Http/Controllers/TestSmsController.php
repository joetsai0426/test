<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\V1\Backend\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestSmsController 
{

    public function test(Request $request)
    {
        // 寄送測試用資料
        $data = [
            'target' => "+886981895891",
            'content' => "[測試]這是用e8d的簡訊內容重構1021-3",
        ];

        $a = 3;

        if( $a > 3 )
        {
            $a== 2;


            dd($a);

        }

        dd('測試用內容!' );
   
                
    }

}
