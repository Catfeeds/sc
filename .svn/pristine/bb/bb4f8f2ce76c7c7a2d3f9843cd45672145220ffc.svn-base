<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\UserLog;
use App\Models\Tag;
use Request;


class TagController extends BaseController
{
    protected $base_url = '/admin/tags';
    protected $view_path = 'admin.tags';

    public function index()
    {
        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        $tags = Tag::TYPE;
        return view('admin.tags.create', ['base_url' => $this->base_url, 'tags' => $tags]);
    }

    public function edit($id)
    {
        $tags = Tag::TYPE;
        $tag = Tag::find($id);

        return view('admin.tags.edit', ['content' => $tag, 'base_url' => $this->base_url, 'tags' => $tags]);
    }

    public function store()
    {
        $input = Request::all();

        $tag = Tag::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '标签', $tag->id, Tag::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();
        $tag = Tag::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '标签', $tag->id, Tag::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function save($id)
    {
        $tag = Tag::find($id);

        if (empty($tag)) {
            return;
        }

        $tag->update(Request::all());
    }

    public function sort()
    {
        return Tag::sort();
    }

    public function state()
    {
        $input = request()->all();
        Tag::state($input);

        $ids = $input['ids'];
        $stateName = Tag::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '标签' . UserLog::ACTION_STATE . ':' . $stateName, $id, Tag::MODEL_CLASS));
        }

    }

    public function table()
    {
        return Tag::table();
    }
}