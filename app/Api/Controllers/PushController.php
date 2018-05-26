<?php

namespace App\Api\Controllers;

use App\Libraries\aliPush\AliPushTool;
use Exception;
use Request;

class PushController extends BaseController
{
    public function pushToOne()
    {
        $result = AliPushTool::instance()->push('ALL', 'NOTICE', '通知测试1', '测试内容1', 'ACCOUNT', '18513103675', function ($request) {

        });
        dd($result);
    }

    public function pushToAll()
    {
        //调用模板，发送推送

    }
}