<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\Gallery;
use App\Models\Category;
use App\Models\UserLog;
use App\Models\Item;
use App\Models\Image;
use Response;
use Request;
use App;
use Auth;
use Gate;

/**
 * 图集
 */
class GalleryController extends Controller
{
    protected $base_url = '/admin/galleries';
    protected $view_path = 'admin.galleries';

    public function __construct()
    {
    }

    public function index()
    {
        if (Gate::denies('@gallery')) {
            return abort(403);
        }

        return view($this->view_path . '.index', ['base_url' => $this->base_url]);
    }

    public function create()
    {
        if (Gate::denies('@gallery-create')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        return view('admin.galleries.create', ['base_url' => $this->base_url]);
    }

    public function edit($id)
    {
        if (Gate::denies('@gallery-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $gallery = Gallery::find($id);
        $gallery->source = $gallery->getSourceName();

        return view('admin.galleries.edit', ['gallery' => $gallery, 'base_url' => $this->base_url, 'back_url' => $this->base_url . '?category_id=' . $gallery->category_id]);
    }

    public function store()
    {
        $input = Request::all();
        $input['source'] = Gallery::SOURCE_USER;
        $input['uploader_id'] = Auth::user()->id;

        $gallery = Gallery::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '图集', $gallery->id, Gallery::MODEL_CLASS));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url . '?category_id=' . $gallery->category_id);
    }

    public function update($id)
    {
        $input = Request::all();

        $gallery = Gallery::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '图集', $gallery->id, Gallery::MODEL_CLASS));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url . '?category_id=' . $gallery->category_id);
    }

    public function comments($refer_id)
    {
        $refer_type = Gallery::MODEL_CLASS;
        return view('admin.comments.list', compact('refer_id', 'refer_type'));
    }

    public function save($id)
    {
        $gallery = Gallery::find($id);

        if (empty($gallery)) {
            return;
        }

        $gallery->update(Request::all());
    }

    public function sort()
    {
        return Gallery::sort();
    }

    public function state()
    {
        $input = request()->all();
        Gallery::state($input);

        $ids = $input['ids'];
        $stateName = Gallery::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '图集' . UserLog::ACTION_STATE . ':' . $stateName, $id, Gallery::MODEL_CLASS));
        }
    }

    public function table()
    {
        return Gallery::table();
    }

    public function categories()
    {
        return Response::json(Category::tree(1, 0, '图库管理', false));
    }

    public function import()
    {
        set_time_limit(0);
        $input = request()->all();
        $import_file = 'public' . $input['file_url'];

        $excel = App::make('excel');

        $excel->load($import_file, function ($reader) {

            $reader = $reader->getSheet(0);//excel第一张sheet
            $results = $reader->toArray();
            unset($results[0]);//去除表头

            if ($results) {
                $count = 0;
                foreach ($results as $key => $value) {
                    $gallery = Gallery::where('name', $value[0])->first();
                    $input['gallery_id'] = $gallery->id;
                    $input['author'] = $value[2];
                    $input['title'] = $value[3];
                    $input['intro'] = $value[4];
                    $input['is_cover'] = $value[5];
                    $input['state'] = Gallery::STATE_PUBLISHED;
                    $input['sort'] = time();
                    $input['uploader_id'] = Auth::user()->id;
                    $input['view_num'] = 0;
                    $input['comment_num'] = 0;
                    $input['like_num'] = 0;
                    $input['collect_num'] = 0;

                    $image = Image::create($input);
                    if (!$image) {
                        echo '图集：' . $value[0] . ' 下的' . $value[1] . '插入失败<br/>';
                    }

                    $item['url'] = 'gallery/2018-05/' . $value[1];
                    $item['type'] = App\Models\Item::TYPE_IMAGE;
                    $item['size'] = 0;
                    $item['w'] = 0;
                    $item['h'] = 0;

                    $image->items()->create($item);
                    $count++;
                }
                $gallery->pic_num += $count;
                $gallery->save();
                echo '完成' . $count . '条数据导入，请稍后更新数据';
            }

        });
    }

    public function updateItem()
    {
        set_time_limit(0);
        $default_num = 50;
        $urls = [];
        $count = 0;
        $size = $w = $h = 0;

        $items = Item::where('type', App\Models\Item::TYPE_IMAGE)
            ->where('refer_type', 'App\Models\Image')
            ->where('size', $size)
            ->where('w', $w)
            ->where('h', $h)
            ->orderBy('id', 'desc')
            ->limit($default_num)
            ->get();

        $item_count = Item::where('type', App\Models\Item::TYPE_IMAGE)
            ->where('refer_type', 'App\Models\Image')
            ->where('size', $size)
            ->where('w', $w)
            ->where('h', $h)
            ->count();


        foreach ($items as $item) {
            $urls[$item->id] = config('site.oss.host') . $item['url'] . '?x-oss-process=image/info';
        }

        $result = $this->curl_multi($urls);
        $data = $result['data'];
        $diff_time = $result['time'];

        //更新数据
        foreach ($data as $key => $val) {
            $item = Item::find($key);
            $val = json_decode($val);

            $item->size = $val->FileSize->value ?: 0;
            $item->w = $val->ImageWidth->value ?: 0;
            $item->h = $val->ImageHeight->value ?: 0;

            $item->save();
            $count++;
        }

        return $this->responseSuccess('', '本次执行时间为' . $diff_time . ' ,更新了' . $count . '条数据，剩余' . ($item_count - $count) . '条数据未更新');

    }

    public function curl_multi($urls)
    {
        $count = 0;

        $mh = curl_multi_init();
        //开始时间
        $startime = getmicrotime();

        foreach ($urls as $i => $url) {
            $conn[$i] = curl_init($url);

            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($conn[$i], CURLOPT_MAXREDIRS, 7); //HTTp定向级别

            curl_setopt($conn[$i], CURLOPT_HEADER, 0); //这里不要header，加块效率

            curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect

            curl_multi_add_handle($mh, $conn[$i]);
        }
        //3.执行curl
        $active = null;
        do {

            $mrc = curl_multi_exec($mh, $active);
            $count++;

        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active and $mrc == CURLM_OK) {
            if (curl_multi_select($mh) === -1) {
                usleep(100);
            }

            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
        // 获取结果
        foreach ($conn as $i => $val) {
            $res[$i] = curl_multi_getcontent($val);
        }

        //4.关闭子curl
        foreach ($conn as $i => $val) {
            curl_multi_remove_handle($mh, $val);
        }

        //5.关闭父curl
        curl_multi_close($mh);

        $endtime = getmicrotime();

        $diff_time = $endtime - $startime;

        return ['time' => $diff_time, 'data' => $res];
    }
}
