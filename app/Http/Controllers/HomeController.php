<?php

namespace App\Http\Controllers;

use Request;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function subnames()
    {
        $term = Request::get('term');

        $gallery = config('site.gallery');

        if ($term == '') {
            foreach ($gallery as $k => $v) {
                $data[$k]['id'] = $v['subname'];
                $data[$k]['subname'] = $v['subname'];
                $data[$k]['cover'] = $v['cover'];
            }
            return $this->response($data);
        }

        $gallery = array_filter($gallery, function ($item) use ($term) {
            return strstr($item['subname'], $term) !== false;
        });

        $data = [];
        foreach ($gallery as $k => $v) {
            $data[$k]['id'] = $v['subname'];
            $data[$k]['subname'] = $v['subname'];
            $data[$k]['cover'] = $v['cover'];
        }

        return $this->response($data);
    }

}
