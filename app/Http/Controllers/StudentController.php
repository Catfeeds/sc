<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use App\Models\UserLog;
use Request;
use Auth;


/**
 * 学生
 */
class StudentController extends Controller
{
    protected $base_url = '/admin/students';
    protected $view_path = 'admin.students';

    public function __construct()
    {
    }

    public function index()
    {
        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        //获取班级列表
        $user = Auth::user();

        if($user->type == User::TYPE_TEACHER){
            $cur_user = $user->id;
        }

        $grades = Grade::select('id', 'name');

        if(isset($cur_user)){
            $grades = $grades->where('member_id', $cur_user)->get()->toArray();
        }
        else{
            $grades = $grades->get()->toArray();
        }

        $grades =array_column($grades, 'name', 'id');

        //获取最大学生编号 +1
        $user = User::withTrashed()->orderBy('user_id', 'desc')->select('user_id')->first();
        $user_id = $user->user_id + 1;

        return view('admin.students.create', ['base_url' => $this->base_url, 'grades' => $grades, 'user_id' => $user_id]);
    }

    public function edit($id)
    {
        $student = Student::find($id);

        $user_id = $student->user_id;

        //获取班级列表
        $user = Auth::user();

        if($user->type == User::TYPE_TEACHER){
            $cur_user = $user->id;
        }

        $grades = Grade::select('id', 'name');

        if(isset($cur_user)){
            $grades = $grades->where('member_id', $cur_user)->get()->toArray();
        }
        else{
            $grades = $grades->get()->toArray();
        }

        $grades =array_column($grades, 'name', 'id');

        return view('admin.students.edit', ['student' => $student, 'base_url' => $this->base_url, 'grades' => $grades, 'user_id' => $user_id]);
    }

    public function store()
    {
        $input = Request::all();

        $student = Student::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '学生', $student->id, Student::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();
        $student = Student::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '学生', $student->id, Student::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $student = Student::find($id);

        if (empty($student)) {
            return;
        }

        $student->update(Request::all());
    }

    public function state()
    {
        $input = request()->all();
        Student::state($input);

        $ids = $input['ids'];

        $stateName = Student::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '学生' . UserLog::ACTION_STATE . ':' . $stateName, $id, Student::MODEL_CLASS));
        }
    }

    public function table()
    {
        return Student::table();
    }
}
