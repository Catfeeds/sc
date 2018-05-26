<?php

namespace App\Models;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Response;

class Tagged extends Model
{

    protected $fillable = [
        'refer_id',
        'refer_type',
        'tags',
    ];

    protected $table = 'tagged';

    public function images()
    {
        return $this->belongsToMany(Image::class);
    }

    public function refer()
    {
        return $this->morphTo();
    }

    public static function sync($content, $tags)
    {
        if (is_array($tags)) {
            $content->tags()->delete();
            foreach ($tags as $tag) {
                $content->tags()->create([
                    'site_id' => $content->site_id,
                    'name' => $tag,
                ]);
            }
        }
    }

}