<?php

namespace App\Models;

use Request;
use Response;
use Auth;

class Grade extends BaseModule
{
    const MODEL_CLASS = 'App\Models\Grade';

    protected $fillable = [
        'name',
        'student_num',
        'school_id',
        'member_id',
        'opened_at',
    ];

    protected $table = 'grades';

    protected $entities = ['member_id', 'school_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function member(){
        return $this->belongsTo(User::class);
    }

    public static function stores($input)
    {
        $grade = static::create($input);

        return $grade;
    }

    public static function updates($id, $input)
    {
        $grade = static::find($id);

        $grade->update($input);

        return $grade;
    }

    public static function table()
    {
        $filters = Request::all();
        $user = Auth::user();

        if($user->type == User::TYPE_PRESIDENT){
            //获取当前登录校长ID
            $school_id = School::where('member_id', $user->id)->select('id')->get()->toArray();
            $school_id = array_column($school_id, 'id');
        }

        if($user->type == User::TYPE_TEACHER){
            //获取当前登录教师ID
            $user_id = $user->id;
        }

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $grades = static::orderBy('id', 'desc')
            ->skip($offset)
            ->limit($limit);

        $total = static::filter($filters);

        if(isset($user_id)){        //显示当前登录教师所属的班级
            $grades = $grades->where('member_id', $user_id)->get();
            $ds->total = $total->where('member_id', $user_id)->count();
        }
        elseif(isset($school_id)){  //显示当前登录校长所属学校的班级
            $grades = $grades->whereIn('school_id', $school_id)->get();
            $ds->total = $total->whereIn('school_id', $school_id)->count();
        }
        else{                       //显示所有班级
            $grades = $grades->get();
            $ds->total = $total->count();
        }

        $grades->transform(function ($grade) {
            $attributes = $grade->getAttributes();

            //实体类型
            foreach ($grade->entities as $entity) {
                $entity_map = str_replace('_id', '_name', $entity);
                $entity = str_replace('_id', '', $entity);

                $attributes[$entity_map] = empty($grade->$entity) ? '' : $grade->$entity->name;
            }

            //日期类型
            foreach ($grade->dates as $date) {
                $attributes[$date] = empty($grade->$date) ? '' : $grade->$date->toDateTimeString();
            }

            $attributes['created_at'] = empty($grade->created_at) ? '' : $grade->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($grade->updated_at) ? '' : $grade->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $grades;

        return Response::json($ds);
    }

    public static function state($input)
    {
        $ids = $input['ids'];
        $state = $input['state'];

        $items = static::whereIn('id', $ids)
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
