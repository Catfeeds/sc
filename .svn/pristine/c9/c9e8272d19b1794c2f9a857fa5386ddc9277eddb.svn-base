<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\Gallery;
use App\Models\Image;
use App\Models\Category;
use App\Models\Tag;
use App\Models\UserLog;
use Auth;
use Gate;
use Request;
use Response;

/**
 * 图片
 */
class ImageController extends Controller
{
    protected $base_url = '/admin/images';
    protected $index_url = '/admin/gallery/images';
    protected $view_path = 'admin.images';

    public function __construct()
    {
    }

    public function index($gallery_id)
    {
        $gallery = Gallery::find($gallery_id);
        return view($this->view_path . '.index', ['gallery' => $gallery, 'base_url' => $this->base_url . '/' . $gallery_id]);
    }

    public function create($gallery_id)
    {
        $tags = Tag::tags(Tag::TYPE_IMG);
        return view('admin.images.create', ['content' => $gallery_id, 'base_url' => $this->base_url, 'tags' => $tags]);
    }

    public function edit($id)
    {
        if (Gate::denies('@image-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $tags = Tag::tags(Tag::TYPE_IMG);

        $image = Image::find($id);

        $tag = $image->tagged()->select('tags')->first();
        if ($tag) {
            $tag = $tag->tags;
            $tagArray = explode('|', $tag);
            $image->tags = $tagArray;
        }

        $item = $image->items()->first();
        $image->url = $item->url . '|' . $item->size . '|' . $item->w . '|' . $item->h;
        $image->img_url = $item->url;

        return view('admin.images.edit', ['content' => $image, 'base_url' => $this->base_url, 'tags' => $tags]);
    }

    public function store($gallery_id)
    {
        $input = Request::input();
        $input['gallery_id'] = $gallery_id;
        $input['uploader_id'] = Auth::user()->id;

        $arr = explode('|', $input['url']);

        $input['image_url'] = isset($arr[0]) ? $arr[0] : 0;
        $input['size'] = isset($arr[1]) ? $arr[1] : 0;
        $input['w'] = isset($arr[2]) ? $arr[2] : 0;
        $input['h'] = isset($arr[3]) ? $arr[3] : 0;

        $image = Image::stores($input);

        Gallery::find($gallery_id)->increment('pic_num');
        event(new UserLogEvent(UserLog::ACTION_CREATE . '图片', $image->id, Image::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->index_url . '/' . $gallery_id);
    }

    public function update($id)
    {
        $input = Request::all();

        $arr = explode('|', $input['url']);

        $input['image_url'] = isset($arr[0]) ? $arr[0] : 0;
        $input['size'] = isset($arr[1]) ? $arr[1] : 0;
        $input['w'] = isset($arr[2]) ? $arr[2] : 0;
        $input['h'] = isset($arr[3]) ? $arr[3] : 0;

        $image = Image::updates($id, $input);

        $gallery_id = Image::find($id)->gallery()->first()->id;

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '图片', $image->id, Image::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->index_url . '/' . $gallery_id);
    }

    public function comments($refer_id)
    {
        $refer_type = Image::MODEL_CLASS;
        return view('admin.comments.list', compact('refer_id', 'refer_type'));
    }

    public function save($id)
    {
        $image = Image::find($id);

        if (empty($image)) {
            return;
        }

        $image->update(Request::all());
    }

    public function sort()
    {
        return Image::sort();
    }

    public function top($id)
    {
        $image = Image::find($id);
        $image->top = !$image->top;
        $image->save();
    }

    public function state()
    {
        $input = request()->all();
        Image::state($input);

        $ids = $input['ids'];
        $stateName = Image::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '图片' . UserLog::ACTION_STATE . ':' . $stateName, $id, Image::MODEL_CLASS));
        }
    }

    public function table($gallery_id)
    {
        return Image::table($gallery_id);
    }

    public function categories()
    {
        return Response::json(Category::tree('', 0, ''));
    }
}
