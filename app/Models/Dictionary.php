<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    const ID_ROOT = 0;
    const CHINA_CODE = "CN";
    const SPECIAL_CITY = [
        '北京',
        '上海',
        '天津',
        '重庆',
    ];

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'value',
        'sort',
    ];

    public function dictionaries()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function tree($state = '', $parent_id = 0, $show_parent = true)
    {
        $dictionaries = static::where(function ($query) use ($state) {
                if (!empty($state)) {
                    $query->where('state', $state);
                }
            })
            ->orderBy('sort')
            ->get();

        $parent = static::find($parent_id);
        if (empty($parent)) {
            $root = new Node();
            $root->id = $parent_id;
            $root->text = '所有字典';
        } else {
            $root = new Node();
            $root->id = $parent->id;
            $root->text = $parent->name;
        }

        static::getNodes($root, $dictionaries);

        if ($show_parent) {
            return [$root];
        } else {
            return $root->nodes;
        }
    }

    public static function getNodes($parent, $dictionaries)
    {
        foreach ($dictionaries as $dictionary) {
            if ($dictionary->parent_id == $parent->id) {
                $node = new Node();
                $node->id = $dictionary->id;
                $node->text = $dictionary->name;

                $parent->nodes[] = $node;
                static::getNodes($node, $dictionaries);
            }
        }
    }
}
