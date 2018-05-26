<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\User;
use App\Models\UserLog;
use App\Models\School;
use Request;

/**
 * 学校
 */
class SchoolController extends Controller
{
    protected $base_url = '/admin/schools';
    protected $view_path = 'admin.schools';

    public function __construct()
    {
    }

    public function index()
    {
        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        $member = User::where('type', User::TYPE_PRESIDENT)
            ->select('id','name')
            ->get()
            ->toArray();

        $member = array_column($member, 'name', 'id');

        return view('admin.schools.create', ['base_url' => $this->base_url, 'member' => $member]);
    }

    public function edit($id)
    {
        $school = School::find($id);

        $member = User::where('type', User::TYPE_PRESIDENT)
            ->select('id','name')
            ->get()
            ->toArray();

        $member = array_column($member, 'name', 'id');

        return view('admin.schools.edit', ['school' => $school, 'base_url' => $this->base_url, 'member' => $member]);
    }

    public function store()
    {
        $input = Request::all();

        $school = School::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '学校', $school->id, School::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();
        $school = School::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '学校', $school->id, School::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $school = School::find($id);

        if (empty($school)) {
            return;
        }

        $school->update(Request::all());
    }

    public function state()
    {
        $input = request()->all();
        School::state($input);

        $ids = $input['ids'];

        $stateName = School::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '学校' . UserLog::ACTION_STATE . ':' . $stateName, $id, School::MODEL_CLASS));
        }
    }

    public function table()
    {
        return School::table();
    }

}
