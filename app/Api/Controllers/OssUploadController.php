<?php

namespace App\Api\Controllers;

use App\Libraries\OssServer\OssServer;
use Exception;

class OssUploadController extends BaseController
{
    public function getUploadToken()
    {
        try {
            $member = \JWTAuth::parseToken()->authenticate();
            if (!$member) {
                return $this->responseError('无效的token,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 401);
        }

        $oss = new OssServer();
        $response = $oss->getToken();

        if ($response['status'] == 200) {
            return $this->responseSuccess($response);
        }
        return $this->responseError('获取上传token失败');
    }

}
