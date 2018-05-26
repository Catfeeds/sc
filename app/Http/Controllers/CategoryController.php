<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\DataSource;
use App\Models\Module;
use DB;
use Gate;
use Request;
use Response;

class CategoryController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        if (Gate::denies('@category')) {
            $this->middleware('deny403');
        }

        //获取当前栏目ID
        $category_id = Request::get('category_id') ?: 0;

        return view('admin.categories.index', compact('category_id'));
    }

    public function create($category_id)
    {
        return view('admin.categories.create', compact('category_id'));
    }

    public function store(Request $request)
    {
        $input = request::all();
        $category_id = $input['category_id'];

        $sort = category::select(db::raw('max(sort) as max'))
            ->where('parent_id', '=', $category_id)
            ->first()->max;

        $sort += 1;

        $input['sort'] = $sort;
        $input['parent_id'] = $category_id;
        $input['type'] = $input['sort'];
        $input['state'] = Category::STATE_ENABLED;

        category::create($input);

        $url = '/admin/categories?category_id=' . $category_id;

        \Session::flash('flash_success', '添加成功');
        return redirect($url);
    }

    public function edit($id)
    {
        $category = Category::find($id);

        if (empty($category)) {
            \Session::flash('flash_warning', '无此记录');

            return redirect('/admin/categories');
        }
        $modules = Module::with('fields')
            ->where('state', Module::STATE_ENABLE)
            ->whereHas('fields', function ($query) {
                $query->where('name', 'category_id');
            })
            ->pluck('title', 'id')
            ->toArray();

        return view('admin.categories.edit', compact('category', 'modules'));
    }


    public function update($id, CategoryRequest $request)
    {
        $category = Category::find($id);

        if ($category == null) {
            \Session::flash('flash_warning', '无此记录');
            return redirect()->to($this->getRedirectUrl())
                ->withInput($request->input());
        }

        $input = Request::all();

        $category->update($input);

        $category_id = $category->parent_id > 0 ? $category->parent_id : $category->id;

        \Session::flash('flash_success', '修改成功!');

        $url = '/admin/categories?category_id=' . $category_id;

        return redirect($url);
    }

    public function save($id)
    {
        $category = Category::find($id);

        if (empty($category)) {
            return;
        }

        $category->update(Request::all());
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category == null) {
            \Session::flash('flash_warning', '无此记录');
            return;
        }

//        if ($category->children()->count() > 0) {
//            \Session::flash('flash_warning', '此栏目有子栏目,不允许删除该栏目');
//            return;
//        }

        $query = call_user_func([$category->module->model_class, 'where'], 'category_id', $id);
//        if ($query->count() > 0) {
//            \Session::flash('flash_warning', '此栏目已有内容,不允许删除该栏目');
//            return;
//        }

        $category->delete();
        \Session::flash('flash_success', '删除成功');
    }

    public function tree()
    {
        return Response::json(Category::tree(1, '0', '', false));
    }

    public function lists($category_id)
    {
        $category = Category::find($category_id);
        if (empty($category)) {
            abort(404);
        }
        return view("admin.templates.categories.list", compact('category'));
    }

    public function table($category_id)
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('sort')
            ->get();

        $categories->transform(function ($category) {
            return [
                'id' => $category->id,
                'type' => $category->type,
                'name' => $category->name,
                'image_url' => $category->image_url,
                'parent_id' => $category->parent_id,
                'cover_url' => $category->cover_url,
                'description' => $category->description,
                'state_name' => $category->stateName(),

                'sort' => $category->sort,
            ];
        });

        $ds = new DataSource();
        $ds->data = $categories;

        return Response::json($ds);
    }

    private function getChildren($all, $parent)
    {
        $result = array();
        foreach ($all as $item) {
            if ($item->parent_id == $parent->id) {
                $result[count($result)] = $item;
                $child = $this->getChildren($all, $item);
                $result = array_merge($result, $child);
            }
        }
        return $result;
    }

}

