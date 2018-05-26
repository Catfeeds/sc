<?php

namespace App\Models;

class MemberDetail extends BaseModule
{
    protected $city;
    protected $fillable = [
        'member_id',
        'province',
        'city',
        'district',
        'school',
        'studio',
        'birtyday',
        'signature',
    ];

    protected $table = 'member_detail';

    public function getCityAttribute()
    {
        $detail = $this->getAttributes();
        $city = Dictionary::find($detail['city']);
        if ($city) {
            return $city->value;
        } else {
            return '';
        }
    }

    public function getProvinceAttribute()
    {
        $detail = $this->getAttributes();
        $province = Dictionary::find($detail['province']);
        if ($province) {
            return $province->value;
        } else {
            return '';
        }
    }

    public function getDistrictAttribute()
    {
        $detail = $this->getAttributes();
        $district = Dictionary::find($detail['district']);
        if ($district) {
            return $district->value;
        } else {
            return '';
        }
    }
}