<?php

namespace App\Api\Controllers;

use App\Models\Dictionary;
use Request;

class DictionaryController extends BaseController
{
    public function transform($dictionaries)
    {
        $dictionaries->transform(function ($dictionary) {

            $childrens = $dictionary->dictionaries()->select('id', 'name', 'value')->get();

            $childrens->transform(function ($children) {
                $children = $children->getAttributes();
                return $children;
            });
            $dictionary->children = $childrens;
            $dictionary = $dictionary->getAttributes();

            return $dictionary;
        });

        return $dictionaries;

    }

    /**
     * @SWG\Get(
     *   path="/area/list",
     *   summary="获取置顶直播",
     *   tags={"/area 直播"},
     *   @SWG\Response(
     *     response=200,
     *     description="获取地区列表成功"
     *   ),
     *   @SWG\Response(
     *     response="404",
     *     description="没有找到路由"
     *   )
     * )
     */
    public function area()
    {
        $country = Dictionary::where('parent_id', Dictionary::ID_ROOT)
            ->where('code', Dictionary::CHINA_CODE)
            ->select('id', 'name', 'value')
            ->first();
        $provinces = $country->dictionaries()->select('id', 'name', 'value')->get();

        $provinces->transform(function ($province) {
            $cities = $province->dictionaries()->select('id', 'name', 'value')->get();

            $cities = $this->transform($cities);

            $province->children = $cities;
            $province = $province->getAttributes();

            return $province;
        });

        $country->children = $provinces;
        $country = $country->getAttributes();

        if ($country) {
            return $this->responseSuccess($country);
        } else {
            return $this->responseError('未找到地区列表', 404);
        }
    }
}