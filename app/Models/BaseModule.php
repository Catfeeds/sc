<?php

namespace App\Models;

use Cache;
use Carbon\Carbon;
use Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BaseModule extends Model
{
    use SoftDeletes;

    const STATE_DELETED = 0;
    const STATE_PUBLISHED = 9;

    const FREE = 0;

    const STATES = [
        0 => '删除',
    ];

    const STATE_PERMISSIONS = [];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->morphMany(Item::class, 'refer');
    }

    public function images()
    {
        return $this->items()->where('type', Item::TYPE_IMAGE)->select('id', 'url', 'size', 'w', 'h')->get();
    }

    public function audios()
    {
        return $this->items()->where('type', Item::TYPE_AUDIO)->get();
    }

    public function videos()
    {
        return $this->items()->where('type', Item::TYPE_VIDEO)->get();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'refer');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'refer');
    }

    public function getCommentCountAttribute()
    {
        return cache_remember($this->table . "-comment-$this->id", 1, function () {
            return $this->comments()->where('state', Comment::STATE_PASSED)->count();
        });
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'refer');
    }

    public function getFavoriteCountAttribute()
    {
        return cache_remember($this->table . "-favorite-$this->id", 1, function () {
            return $this->favorites()->count();
        });
    }

    public function follows()
    {
        return $this->morphMany(Follow::class, 'refer');
    }

    public function getFollowCountAttribute()
    {
        return cache_remember($this->table . "-follow-$this->id", 1, function () {
            return $this->follows()->count();
        });
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'refer');
    }

    public function getLikeCountAttribute()
    {
        return cache_remember($this->table . "-like-$this->id", 1, function () {
            return $this->likes ? $this->likes->count : 0;
        });
    }

    public function getClickCountAttribute()
    {
        return Cache::rememberForever($this->table . "-click-$this->id", function () {
            return $this->clicks ? $this->clicks->count : 0;
        });
    }

    public function incrementClick()
    {
        $count = Cache::increment($this->table . "-click-$this->id");
        //间隔回写数据库
        if ($count % env('CLICK_INTERVAL', 1) == 0) {
            if (empty($this->clicks)) {
                $this->clicks()->create([
                    'count' => $count,
                ]);
            } else {
                $this->clicks->increment('count');
            }
        }
    }

    public function getStateNameAttribute()
    {
        return array_key_exists($this->state, static::STATES) ? static::STATES[$this->state] : '';
    }

    public function setCreatedAt($value)
    {
        if (in_array('sort', $this->fillable)) {
            $this->attributes['sort'] = strtotime($value);
        }
        return parent::setCreatedAt($value);
    }

    public function stateName()
    {
        return array_key_exists($this->state, static::STATES) ? static::STATES[$this->state] : '';
    }

    public function scopeFilter($query, $filters)
    {
        $filters['order'] = empty($filters['order']) ? '<' : '>';

        $query->where(function ($query) use ($filters) {
            empty($filters['id']) ?: $query->where('id', $filters['id']);
            empty($filters['category_id']) ?: $query->where('category_id', $filters['category_id']);
            empty($filters['title']) ?: $query->where('title', 'like', '%' . $filters['title'] . '%');
            empty($filters['is_free']) ?: $query->where('is_free', $filters['is_free']);
            empty($filters['recommended']) ?: $query->where('recommended', $filters['recommended']);
            empty($filters['last_id']) ?: $query->where('id', $filters['order'], $filters['last_id']);
            empty($filters['start_date']) ?: $query->where('created_at', '>=', $filters['start_date']);
            empty($filters['end_date']) ?: $query->where('created_at', '<=', $filters['end_date']);
            empty($filters['user_name']) ?: $query->whereHas('user', function ($query) use ($filters) {
                $query->where('name', $filters['user_name']);
            });
        });
        if (isset($filters['state'])) {
            if (!empty($filters['state'])) {
                $query->where('state', $filters['state']);
            } else if ($filters['state'] === strval(static::STATE_DELETED)) {
                $query->onlyTrashed();
            }
        }
    }

    public static function getStateName($state)
    {
        return array_key_exists($state, static::STATES) ? static::STATES[$state] : '';
    }

    public static function state($input)
    {
        $ids = $input['ids'];
        $state = $input['state'];

        //判断是否有操作权限
        $permission = array_key_exists($state, static::STATE_PERMISSIONS) ? static::STATE_PERMISSIONS[$state] : '';
        if (!empty($permission) && Gate::denies($permission)) {
            return;
        }

        $items = static::whereIn('id', $ids)
            ->get();
        foreach ($items as $item) {
            $item->state = $state;
            $item->save();
            if ($state == static::STATE_DELETED) {
                $item->delete();
            } else if ($item->trashed()) {
                $item->restore();
            }
            if ($state == static::STATE_PUBLISHED) {
                $item->published_at = Carbon::now();
                $item->save();
            }
        }
    }

    public static function click($id)
    {
        $object = static::find($id);
        if (empty($object)) {
            return;
        }

        $object->incrementClick();
    }


    public function precheck($member)
    {
        //默认用户订单不存在
        $exist = '';
        //检查用户是否有权限购买
        if ($member->state == Member::STATE_DISABLED) {
            return false;
        }
        //检查用户是否已购买，购买过直播和课程则不能购买  加状态，取消购买返回旧订单
        $memberPaid = $this->carts()->where('state', Cart::STATE_PAID)->pluck('member_id')->toArray();
        $memberNopay = $this->carts()->where('state', Cart::STATE_NOPAY)->pluck('member_id')->toArray();

        //因为线下课程可以重复购买
        if (($this instanceof Live || $this instanceof Course) && in_array($member->id, $memberPaid)) {
            return false;
        }

        if (in_array($member->id, $memberNopay)) {
            $cart = $this->carts()
                ->where('state', Cart::STATE_NOPAY)
                ->where('member_id', $member->id)
                ->first();

            $exist = $cart->order()->first();
        }

        //返回打包数据data,暂时返回true
        return [
            'member_id' => $member->id,
            'name' => $this->title,
            'image' => empty($this->cover_url) ?: $this->cover_url,
            'price' => $this->price,
            'num' => Cart::NUM_DEFAULT,
            'amount' => $this->price * Cart::NUM_DEFAULT,
            'detail' => $this->intro,
            'state' => Cart::STATE_NOPAY,
            'exist' => $exist,
        ];
    }

}