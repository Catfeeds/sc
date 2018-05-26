<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const ID_ROOT = 0;
    const GOODS_ID = 1;

    const STATE_DISABLED = 0;
    const STATE_ENABLED = 1;

    const TYPE_ARTICLE = 0;
    const TYPE_COURSE = 1;
    const TYPE_LIVE = 2;
    const TYPE_GALLERY = 3;
    const TYPE_INDEX = 4;

    const TYPES = [
        0 => '文章',
        1 => '课程',
        2 => '直播',
        3 => '图集',
        4 => '首页',
    ];

    const STATES = [
        1 => '已启用',
        0 => '已禁用',
    ];

    const LINK_TYPE_NONE = 0;
    const LINK_TYPE_WEB = 1;

    const LINK_TYPES = [
        0 => '无',
        1 => '网址',
    ];

    protected $fillable = [
        'site_id',
        'type',
        'code',
        'name',
        'module_id',
        'title',
        'subtitle',
        'image_url',
        'cover_url',
        'author',
        'description',
        'content',
        'likes',
        'state',
        'sort',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'module_id');
    }

    public function goods()
    {
        return $this->hasMany(Goods::class, 'category_id');
    }

    public function scopeOwns($query)
    {
        if (Auth::user()->roles()->where('id', Role::ID_ADMIN)->exists()) {
            $query->where('site_id', Auth::user()->site_id);
        } else {
            $category_ids = Auth::user()->categories->pluck('id')->toArray();
            $query->where('site_id', Auth::user()->site_id)->whereIn('id', $category_ids);
        }
    }

    public function stateName()
    {
        return array_key_exists($this->state, static::STATES) ? static::STATES[$this->state] : '';
    }

    public static function tree($state = 1, $parent_id = 0, $modules, $show_parent = true)
    {
        if (empty($modules)) {
            $categories = Category::where('state', $state)
                ->orderBy('sort')
                ->get();
        } elseif ($modules > 0) {
            $categories = Category::where('state', $state)
                ->where('module_id', $modules)
                ->orderBy('sort')
                ->get();
        } else {
            $cat = Category::where('state', $state)->where('name', $modules)->first()['id'];
            $categories = Category::where('state', $state)->where('name', $modules)->orWhere('parent_id', $cat)->get();
        }

        $parent = Category::find($parent_id);
        if (empty($parent)) {                          //
            $root = new Node();
            $root->id = $parent_id;
            $root->text = '所有分类';
        } else {                                         //
            $root = new Node();
            $root->id = $parent->id;
            $root->text = $parent->name;
        }


        static::getNodes($root, $categories);

        if ($show_parent) {
            return [$root];
        } else {
            return $root->nodes;
        }
    }

    public static function getNodes($parent, $categories)
    {
        foreach ($categories as $category) {
            if ($category->parent_id == $parent->id) {
                $node = new Node();
                $node->id = $category->id;
                $node->text = $category->name;
                $node->tags = [$category->module->title];

                $parent->nodes[] = $node;
                static::getNodes($node, $categories);
            }
        }

    }
}
