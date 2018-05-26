<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\Grade;
use App\Models\School;
use App\Models\User;
use App\Models\UserLog;
use Request;
use Auth;


/**
 * 班级
 */
class GradeController extends Controller
{
    protected $base_url = '/admin/grades';
    protected $view_path = 'admin.grades';

    public function __construct()
    {
    }

    public function index()
    {
        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        //获取教师列表
        $members = User::where('type', User::TYPE_TEACHER)
            ->select('id','name')
            ->get()
            ->toArray();

        $members = array_column($members, 'name', 'id');

        //获取学校列表
        $user = Auth::user();

        if($user->type == User::TYPE_TEACHER){
            $user_id = $user->id;
        }

        $schools = School::select('id', 'name');

        if(isset($user_id)){
            $schools = $schools->where('member_id', $user_id)->get()->toArray();
        }
        else{
            $schools = $schools->get()->toArray();
        }

        $schools =array_column($schools, 'name', 'id');


        return view('admin.grades.create', ['base_url' => $this->base_url, 'members' => $members, 'schools' => $schools]);
    }

    public function edit($id)
    {
        $grade = Grade::find($id);

        //获取教师列表
        $members = User::where('type', User::TYPE_TEACHER)
            ->select('id','name')
            ->get()
            ->toArray();

        $members = array_column($members, 'name', 'id');

        //获取学校列表
        $user = Auth::user();

        if($user->type == User::TYPE_TEACHER){
            $user_id = $user->id;
        }

        $schools = School::select('id', 'name');

        if(isset($user_id)){
            $schools = $schools->where('member_id', $user_id)->get()->toArray();
        }
        else{
            $schools = $schools->get()->toArray();
        }

        $schools =array_column($schools, 'name', 'id');

        return view('admin.grades.edit', ['grade' => $grade, 'base_url' => $this->base_url, 'members' => $members, 'schools' => $schools]);
    }

    public function store()
    {
        $input = Request::all();

        $grade = Grade::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '班级', $grade->id, Grade::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();
        $grade = Grade::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '班级', $grade->id, Grade::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $grade = Grade::find($id);

        if (empty($grade)) {
            return;
        }

        $grade->update(Request::all());
    }

    public function state()
    {
        $input = request()->all();
        Grade::state($input);

        $ids = $input['ids'];

        $stateName = Grade::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '班级' . UserLog::ACTION_STATE . ':' . $stateName, $id, Grade::MODEL_CLASS));
        }
    }

    public function table()
    {
        return Grade::table();
    }
}
