<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\UserLog;
use App\Models\Pic;
use Request;
use Response;
use App\Models\Category;

/**
 * 首页
 */
class PicController extends Controller
{
    protected $base_url = '/admin/pics';
    protected $view_path = 'admin.pics';

    public function __construct()
    {
    }

    public function index()
    {
        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        return view('admin.pics.create', ['base_url' => $this->base_url]);
    }

    public function edit($id)
    {
        $index = Pic::find($id);

        return view('admin.pics.edit', ['content' => $index, 'base_url' => $this->base_url]);
    }

    public function store()
    {
        $input = Request::all();

        $index = Pic::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '首页', $index->id, Pic::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();
        $index = Pic::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '首页', $index->id, Pic::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $live = Pic::find($id);

        if (empty($live)) {
            return;
        }

        $live->update(Request::all());
    }

    public function sort()
    {
        return Pic::sort();
    }

    public function state()
    {
        $input = request()->all();
        Pic::state($input);

        $ids = $input['ids'];
        $stateName = Pic::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '首页' . UserLog::ACTION_STATE . ':' . $stateName, $id, Pic::MODEL_CLASS));
        }

    }

    public function table()
    {
        return Pic::table();
    }

    public function categories()
    {
        return Response::json(Category::tree('1', '0', '首页', false));
    }

}
