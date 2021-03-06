<?php

namespace App\Http\Controllers;

use App\Libraries\wyyIm\IstantMessage;
use App\Events\UserLogEvent;
use App\Models\UserLog;
use App\Models\Live;
use App\Models\Member;
use Carbon\Carbon;
use Request;
use Auth;
use Gate;

class LiveController extends Controller
{
    protected $base_url = '/admin/lives';
    protected $view_path = 'admin.lives';

    public function __construct()
    {
    }

    public function index()
    {
        if (Gate::denies('@live')) {
            return abort(403);
        }

        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        if (Gate::denies('@live-create')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }
        $member = Member::where('type', Member::TYPE_TEACHER)
            ->select('id', 'nick_name', 'name')
            ->get()->toArray();

        $member = array_column($member, 'name', 'id');

        $states = Live::STATES;

        return view('admin.lives.create', ['base_url' => $this->base_url, 'member' => $member, 'states' => $states]);
    }

    public function edit($id)
    {
        if (Gate::denies('@live-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $member = Member::where('type', Member::TYPE_TEACHER)
            ->select('id', 'nick_name', 'name')
            ->get()->toArray();

        $member = array_column($member, 'name', 'id');

        $states = Live::STATES;

        $live = Live::find($id);
        $poster_url = explode('|', $live->poster_url);
        $live->poster_show_url = $poster_url[0];

        $live['duration'] = $live['duration'] / 60;
        $live['price'] = $live['price'] / 100;

        return view('admin.lives.edit', ['member' => $member, 'live' => $live, 'base_url' => $this->base_url, 'states' => $states]);
    }

    public function store()
    {
        $input = Request::all();
        $input['user_id'] = Auth::user()->id;
        $input['duration'] = $input['duration'] * 60;
        $input['price'] = $input['price'] * 100;

        $live = Live::store($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '直播', $live->id, Live::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();
        $input['duration'] = $input['duration'] * 60;
        $input['price'] = $input['price'] * 100;

        $live = Live::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '直播', $live->id, Live::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $live = Live::find($id);

        if (empty($live)) {
            return;
        }

        $live->update(Request::all());
    }

    public function sort()
    {
        return Live::sort();
    }

    public function top($id)
    {
        $live = Live::find($id);
        $live->is_top = !$live->is_top;
        $live->save();
    }

    public function tag($id)
    {
        $tag = request('tag');
        $live = Live::find($id);
        if ($live->tags()->where('name', $tag)->exists()) {
            $live->tags()->where('name', $tag)->delete();
        } else {
            $live->tags()->create([
                'site_id' => $live->site_id,
                'name' => $tag,
                'sort' => strtotime(Carbon::now()),
            ]);
        }
    }

    public function state()
    {
        $input = request()->input();

        $live = Live::find($input['id']);

        //准备直播时，分配直播群
        $imObj = new IstantMessage();

        if ($input['state'] == Live::STATE_PREPARED) {
            $input['room_ids'] = $imObj->createTeam($live);
        } else if ($input['state'] == Live::STATE_END) {
            $imObj->removeTeam($live);
            $input['room_ids'] = '';
        }

        //直播结束时，解散直播群
        Live::state($input);

        $stateName = Live::getStateName($input['state']);

        //记录日志
        event(new UserLogEvent('变更' . '直播' . UserLog::ACTION_STATE . ':' . $stateName, $input['id'], Live::MODEL_CLASS));
    }

    public function table()
    {
        return Live::table();
    }

}
