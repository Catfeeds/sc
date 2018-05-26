<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    const TYPE_IMAGE = 0;
    const TYPE_VIDEO = 1;
    const TYPE_AUDIO = 2;

    protected $fillable = [
        'refer_id',
        'refer_type',
        'type',
        'title',
        'url',
        'summary',
        'size',
        'w',
        'h',
        'sort',
    ];

    public function refer()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->morphMany(Item::class, 'refer');
    }

    public static function sync($type, $content, $urls, $summary = '')
    {
        if (!empty($urls)) {
            $urls = explode('|', trim($urls));

            foreach ($urls as $key => $url) {
                $content->items()->create([
                    'type' => $type,
                    'title' => '',
                    'summary' => $summary,
                    'url' => str_replace(url(''), '', $url),
                ]);
            }
        }
    }
}
