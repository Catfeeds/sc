<?php

namespace App\Api\Controllers;

use App\Models\App;
use Request;
use Exception;
use Validator;


class AppController extends BaseController
{
    public function appCheck($input)
    {
        $rules = [];
        if (isset($input['device'])) {
            $rules['device'] = 'required';
        }
        if (isset($input['version'])) {
            $rules['version'] = 'required';
        }

        $message = [
            'device.required' => 'device不能为空',
            'version.required' => 'version不能为空',
        ];

        $validate = Validator::make($input, $rules, $message);

        if ($validate->fails()) {
            throw new Exception($validate->errors()->first());
        }

        return $input;
    }

    /**
     * @SWG\Get(
     *   path="/app/check",
     *   summary="检查版本信息",
     *   tags={"/app 应用程序"},
     *   @SWG\Parameter(name="device", in="query", required=true, description="用户设备", type="string"),
     *   @SWG\Parameter(name="version", in="query", required=true, description="用户当前版本号", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="没查到数据 返回最新版本信息"
     *   ),
     *   @SWG\Response(
     *     response="200",
     *     description="当前为最新版本"
     *   )
     * )
     */

    public function check()
    {
        $input['device'] = Request::get('device');
        $input['version'] = Request::get('version');

        try {
            $input = $this->appCheck($input);
        } catch (Exception $e) {
            return $this->responseError('参数错误，请检查参数');
        }

        $app = App::where('state', App::STATE_ABLE)
            ->where($input['device'] . '_version', $input['version'])
            ->first();

        $cur_app = App::where('state', App::STATE_ABLE)
            ->select('name', 'logo_url', $input['device'] . '_version', $input['device'] . '_force', $input['device'] . '_url', $input['device'] . '_content', 'created_at', 'updated_at')
            ->first();

        if (empty($app)) {
            $cur_app = $cur_app->getAttributes();
            return $this->responseSuccess($cur_app);
        }

        return $this->responseSuccess('当前为最新版本');


    }
}