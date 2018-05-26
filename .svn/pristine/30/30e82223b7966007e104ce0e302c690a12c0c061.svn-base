<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Jobs\PublishPage;
use App\Models\OfflineCourse;
use App\Models\Category;
use App\Models\Domain;
use App\Models\Module;
use App\Models\UserLog;
use Auth;
use Carbon\Carbon;
use Gate;
use Request;
use Response;

/**
 * 线下课程
 */
class OfflineCourseController extends Controller
{
    protected $base_url = '/admin/offlinecourses';
    protected $view_path = 'admin.offlinecourses';
    protected $module;

    public function __construct()
    {
        $this->module = Module::where('name', 'OfflineCourse')->first();
    }

    public function show(Domain $domain, $id)
    {
        if (empty($domain->site)) {
            return abort(501);
        }

        $offlinecourse = OfflineCourse::find($id);
        if (empty($offlinecourse)) {
            return abort(404);
        }
        $offlinecourse->incrementClick();

        return view('themes.' . $domain->theme->name . '.offlinecourses.detail', ['site' => $domain->site, 'offlinecourse' => $offlinecourse]);
    }

    public function slug(Domain $domain, $slug)
    {
        if (empty($domain->site)) {
            return abort(501);
        }

        $offlinecourse = OfflineCourse::where('slug', $slug)
            ->first();
        if (empty($offlinecourse)) {
            return abort(404);
        }
        $offlinecourse->incrementClick();

        return view('themes.' . $domain->theme->name . '.offlinecourses.detail', ['site' => $domain->site, 'offlinecourse' => $offlinecourse]);
    }

    public function lists(Domain $domain)
    {
        if (empty($domain->site)) {
            return abort(501);
        }

        $offlinecourses = OfflineCourse::where('state', OfflineCourse::STATE_PUBLISHED)
            ->orderBy('sort', 'desc')
            ->get();

        return view('themes.' . $domain->theme->name . '.offlinecourses.index', ['site' => $domain->site, 'module' => $this->module, 'offlinecourses' => $offlinecourses]);
    }

    public function index()
    {
        if (Gate::denies('@offlinecourse')) {
            return abort(403);
        }

        $module = Module::transform($this->module->id);

        return view($this->view_path . '.index', ['module' => $module, 'base_url' => $this->base_url]);
    }

    public function create()
    {
        if (Gate::denies('@offlinecourse-create')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $module = Module::transform($this->module->id);

        return view('admin.contents.create', ['module' => $module, 'base_url' => $this->base_url]);
    }

    public function edit($id)
    {
        if (Gate::denies('@offlinecourse-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $module = Module::transform($this->module->id);

        $offlinecourse = call_user_func([$this->module->model_class, 'find'], $id);
        $offlinecourse->images = null;
        $offlinecourse->videos = null;
        $offlinecourse->audios = null;
        $offlinecourse->tags = $offlinecourse->tags()->pluck('name')->toArray();

        return view('admin.contents.edit', ['module' => $module, 'content' => $offlinecourse, 'base_url' => $this->base_url]);
    }

    public function store()
    {
        $input = Request::all();
        $input['site_id'] = Auth::user()->site_id;
        $input['user_id'] = Auth::user()->id;

        $validator = Module::validate($this->module, $input);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $offlinecourse = OfflineCourse::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '线下课程', $offlinecourse->id, $this->module->model_class));

        \Session::flash('flash_success', '添加成功');
        return redirect($this->base_url);
    }

    public function update($id)
    {
        $input = Request::all();

        $validator = Module::validate($this->module, $input);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $offlinecourse = OfflineCourse::updates($id, $input);

        event(new UserLogEvent(UserLog::ACTION_UPDATE . '线下课程', $offlinecourse->id, $this->module->model_class));

        \Session::flash('flash_success', '修改成功!');
        return redirect($this->base_url);
    }

    public function comments($refer_id)
    {
        $refer_type = $this->module->model_class;
        return view('admin.comments.list', compact('refer_id', 'refer_type'));
    }

    public function save($id)
    {
        $offlinecourse = OfflineCourse::find($id);

        if (empty($offlinecourse)) {
            return;
        }

        $offlinecourse->update(Request::all());
    }

    public function sort()
    {
        return OfflineCourse::sort();
    }

    public function top($id)
    {
        $offlinecourse = OfflineCourse::find($id);
        $offlinecourse->top = !$offlinecourse->top;
        $offlinecourse->save();
    }

    public function tag($id)
    {
        $tag = request('tag');
        $offlinecourse = OfflineCourse::find($id);
        if ($offlinecourse->tags()->where('name', $tag)->exists()) {
            $offlinecourse->tags()->where('name', $tag)->delete();
        } else {
            $offlinecourse->tags()->create([
                'site_id' => $offlinecourse->site_id,
                'name' => $tag,
                'sort' => strtotime(Carbon::now()),
            ]);
        }
    }

    public function state()
    {
        $input = request()->all();
        OfflineCourse::state($input);

        $ids = $input['ids'];
        $stateName = OfflineCourse::getStateName($input['state']);

        //记录日志
        foreach ($ids as $id) {
            event(new UserLogEvent('变更' . '线下课程' . UserLog::ACTION_STATE . ':' . $stateName, $id, $this->module->model_class));
        }

        //发布页面
        $site = auth()->user()->site;
        if ($input['state'] == OfflineCourse::STATE_PUBLISHED) {
            foreach ($ids as $id) {
                $this->dispatch(new PublishPage($site, $this->module, $id));
            }
        }
    }

    public function table()
    {
        return OfflineCourse::table();
    }

    public function categories()
    {
        return Response::json(Category::tree('', 0, $this->module->id));
    }
}
