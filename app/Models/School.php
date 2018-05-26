<?php

namespace App\Models;

use Request;
use Response;
use Auth;

class School extends BaseModule
{
    const MODEL_CLASS = 'App\Models\School';

    const STATES = [
        0 => '删除',
    ];

    protected $fillable = [
        'name',
        'address',
        'phone',
        'member_id',
    ];

    protected $table = 'schools';

    protected $entities = ['member_id'];

    public function member()
    {
        return $this->belongsTo(User::class);
    }

    public static function stores($input)
    {
        $school = static::create($input);

        return $school;
    }

    public static function updates($id, $input)
    {
        $school = static::find($id);

        $school->update($input);

        return $school;
    }

    public static function table()
    {
        $filters = Request::all();
        $user = Auth::user();
        if($user->type == User::TYPE_PRESIDENT){
            //获取当前登录校长ID
            $user_id = $user->id;
        }

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;

        $ds = new DataSource();
        $schools = static::
            orderBy('id', 'desc')
            ->skip($offset)
            ->limit($limit);

        $total = static::filter($filters);

        if(isset($user_id)){    //显示当前登录校长所在的学校
            $schools = $schools->where('member_id', $user_id)->get();
            $ds->total = $total->where('member_id', $user_id)->count();
        }
        else{                   //显示所有学校
            $schools = $schools->get();
            $ds->total = $total->count();
        }

        $schools->transform(function ($school) {
            $attributes = $school->getAttributes();

            //实体类型
            foreach ($school->entities as $entity) {
                $entity_map = str_replace('_id', '_name', $entity);
                $entity = str_replace('_id', '', $entity);

                $attributes[$entity_map] = empty($school->$entity) ? '' : $school->$entity->name;
            }

            //日期类型
            foreach ($school->dates as $date) {
                $attributes[$date] = empty($school->$date) ? '' : $school->$date->toDateTimeString();
            }

            $attributes['created_at'] = empty($school->created_at) ? '' : $school->created_at->toDateTimeString();
            $attributes['updated_at'] = empty($school->updated_at) ? '' : $school->updated_at->toDateTimeString();
            return $attributes;
        });

        $ds->rows = $schools;

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
