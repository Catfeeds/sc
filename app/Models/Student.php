<?php

namespace App\Models;

use Request;
use Response;
use Auth;

class Student extends BaseModule
{
    const MODEL_CLASS = 'App\Models\Student';

    protected $fillable = [
        'name',
        'sex',
        'age',
        'mobile',
        'user_id',
        'grade_id',
    ];

    const STATES = [
        0 => '删除',
    ];

    protected $table = 'users';

    protected $entities = ['grade_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function member()
    {
        return $this->belongsTo(User::class);
    }

    public static function stores($input)
    {
        $input['type'] = User::TYPE_STUDENT;

        $student = Student::create($input);

        return $student;
    }

    public static function updates($id, $input)
    {
        $student = Student::find($id);

        $student->update($input);

        return $student;
    }

    public static function table()
    {
        $filters = Request::all();
        $user = Auth::user();

        if($user->type == User::TYPE_TEACHER){
            //获取当前登录教师ID
            $grade_id = Grade::where('member_id', $user->id)->select('id')->get()->toArray();
            $grade_id = array_column($grade_id, 'id');
        }

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $students = User::where('type', User::TYPE_STUDENT)
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->limit($limit);

        $total = User::filter($filters)->where('type', User::TYPE_STUDENT);

        if(isset($grade_id)){       //显示当前登录教师管理的学生
            $students = $students->whereIn('grade_id', $grade_id)->get();
            $ds->total = $total->whereIn('grade_id', $grade_id)->count();
        }
        else{                       //显示所有学生
            $students = $students->get();
            $ds->total = $total->count();
        }

        $students->transform(function ($student) {
            $attributes = $student->getAttributes();

            //实体类型
            if($student->entities){
                foreach ($student->entities as $entity) {
                    $entity_map = str_replace('_id', '_name', $entity);
                    $entity = str_replace('_id', '', $entity);

//                    $attributes[$entity_map] = empty($student->$entity) ? '' : $student->$entity->name;
                }
            }

            //日期类型
            foreach ($student->dates as $date) {
                $attributes[$date] = empty($student->$date) ? '' : $student->$date->toDateTimeString();
            }

            $attributes['created_at'] = empty($student->created_at) ? '' : $student->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($student->updated_at) ? '' : $student->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $students;

        return Response::json($ds);
    }

    public static function state($input)
    {
        $ids = $input['ids'];
        $state = $input['state'];

        $items = User::whereIn('id', $ids)
            ->get();

        foreach ($items as $item) {
            $item->save();
            if ($state == static::STATE_DELETED) {
                $item->delete();
            } else if ($item->trashed()) {
                $item->restore();
            }
        }
    }

}
