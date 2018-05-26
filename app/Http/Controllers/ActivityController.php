<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\Activity;
use App\Models\UserLog;
use Auth;
use Gate;
use Request;

/**
 * 活动
 */
class ActivityController extends Controller
{
    protected $base_url = '/admin/activities';
    protected $view_path = 'admin.activities';

    public function __construct()
    {
    }

    public function index()
    {
        if (Gate::denies('@article')) {
            return abort(403);
        }

        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        if (Gate::denies('@article-create')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        return view('admin.activities.create', ['base_url' => $this->base_url]);
    }

    public function edit($id)
    {
        if (Gate::denies('@article-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $activity = Activity::find($id);
        $activity->images = null;

        return view('admin.activities.edit', ['content' => $activity, 'base_url' => $this->base_url, 'back_url' => $this->base_url]);
    }

    public function store()
    {
        $input = Request::all();
        $input['user_id'] = Auth::user()->id;
        $activity = Activity::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '活动', $activity->id, Activity::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();

        $activity = Activity::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '活动', $activity->id, Activity::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $activity = Activity::find($id);

        if (empty($activity)) {
            return;
        }

        $activity->update(Request::all());
    }

    public function sort()
    {
        return Activity::sort();
    }

    public function state()
    {
        $input = request()->all();
        Activity::state($input);

        $ids = $input['ids'];
        $stateName = Activity::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '活动' . UserLog::ACTION_STATE . ':' . $stateName, $id, Activity::MODEL_CLASS));
        }
    }

    public function table()
    {
        return Activity::table();
    }

}
