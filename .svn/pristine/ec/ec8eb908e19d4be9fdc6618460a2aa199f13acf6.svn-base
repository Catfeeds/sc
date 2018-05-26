<?php

namespace App\Models;


class Domain
{
    const MARK_MEMBER = 'member';
    const MARK_GOODS = 'goods';
    const MARK_DETAIL = 'detail';

    //public $theme = '';
    public $theme ='default';
    public function __construct( $theme = null)
    {

        $this->theme = Theme::where('name','default')->first();

    }
}