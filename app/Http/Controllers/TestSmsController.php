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
            'target' => "+886900666333",
            'content' => "[測試]這是用e8d的簡訊內容重構1021-3",
        ];

        $a = 3;

        $b = 5;

        if ($a > 3) {
            $a += 2;

            $b += 5;

            dd($a, '測試用的dd');
        }

        dd('測試用內容123!');
    }
}
