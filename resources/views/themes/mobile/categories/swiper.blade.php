@extends('templates.master')
@section('title', '江铃汽车股份有限公司')
@section('css')
    <link href="{{ url('css/swiper.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('css/news_info.css') }}" rel="stylesheet">
    <script src="{{ url('js/swiper.jquery.min.js') }}"></script>
@endsection
@section('content')
    <div class="nav_min_bg"></div>

    <div class="banner">
        <img src="{{ url($category->parent->cover_url) }}">
    </div>

    <div class="content">
        <div class="content_left">
            <h5 class="section_title">OUR INFO</h5>
            <div class="col_short"></div>
            <h4>{{ $category->parent->parent->name }}</h4>
            <div class="info_tabs">
                <ul>
                    @foreach($category->parent->parent->children()->where('state', \App\Models\Category::STATE_ENABLED)->orderBy('sort', 'desc')->get() as $level1)
                        <li class="hide {{ $level1->id == $category->parent->id ? 'bg': '' }}">
                            <a href="javascript:;">{{$level1->name}} </a>
                            <div class="smenu"
                                 @if($level1->id == $category->parent->id) style="display: block;" @endif>
                                <span></span>
                                @foreach($level1->children as $level2)
                                    <a href="{{ url('categories/' . $level2->id) }}"
                                       @if($level2->id == $category->id) style="color:#df0606" @endif
                                    >{{ $level2->name }}</a>
                                @endforeach
                                @foreach($level1->contents()->where('state', \App\Models\Content::STATE_PUBLISHED)->orderBy('sort', 'desc')->get() as $item)
                                    <a @if($item->link_type == 1)
                                       href="{{$item->link}}" target="_blank"
                                       @else
                                       href="{{ url('contents/' . $item->id) }}"
                                       @endif
                                    >{{$item->title}}</a>
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="content_right">
            <div class="location clear">
                <p class="location_left">您所在的位置 <a href="/">首页</a> > {{ $category->parent->parent->name }} > {{ $category->parent->name }} </p>
            </div>
            <h2 class="title">{{ $category->name }}</h2>
            <div style="border-bottom: 1px solid #ededed;"></div>
            <div class="download">
                <div class="showlist0">
                    <div class="list">
                        <ul>
                            @foreach($category->contents()->where('state', \App\Models\Content::STATE_PUBLISHED)->orderBy('sort', 'desc')->get()->take(10) as $content)
                                <div class="item">
                                    <a href="javascript:showZx(0);">{{ $content->title }}</a>
                                </div>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="zxWindow">
        <div class="close"></div>
        <div class="list swiper-container">
            <div class="swiper-wrapper">
                @foreach($category->contents()->where('state', \App\Models\Content::STATE_PUBLISHED)->orderBy('sort', 'desc')->get()->take(10) as $content)
                    <div class="swiper-slide">
                        <img src="{{ get_image_url($content->image_url) }}">
                    </div>
                @endforeach
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var mq;
        var isMinStage = false;
        var nowShowLeftMenu;
        var zxlist;
        $(function () {
            $('.search_one input').focus(function (e) {
                $(this).addClass('long');
            });
            $('.search_one input').blur(function (e) {
                $(this).removeClass('long');
            });
            $(window).scroll(function () {
                var st = $(document.documentElement).scrollTop() || $(document.body).scrollTop();
                if (st > 58) {
                    $('.header').addClass('fix');
                    $('.nav').addClass('fix');
                } else {
                    $('.header').removeClass('fix');
                    $('.nav').removeClass('fix');
                }
            });
            /*min*/
            $('.nav_min_in').click(function (e) {
                if ($(this).hasClass('close')) {
                    $(this).removeClass('close');
                    $('.nav_min').slideUp(300);
                    $('.nav_min_bg').fadeOut('fast');
                } else {
                    $(this).addClass('close');
                    $('.nav_min').slideDown(300);
                    $('.nav_min_bg').fadeIn('fast');
                }
            });
            $('.nav_min_bg').click(function (e) {
                $('.nav_min_in').removeClass('close');
                $('.nav_min').slideUp(300);
                $(this).fadeOut('fast');
            });
            $('.nav_min .i').each(function (index, element) {
                $(element).click(function (e) {
                    var _fd = $(this).find('div');
                    if (_fd.length > 0) {
                        $(_fd[0]).slideToggle('fast');
                    }
                });
            });
            //内容切换
            $('.dmenu span').each(function (index, element) {
                $(element).attr('ids', index).click(function (e) {
                    if ($(this).attr('ids') == 0) {
                        $('.showlist0').show();
                        $('.showlist1').hide();
                    } else {
                        $('.showlist1').show();
                        $('.showlist0').hide();
                    }
                });
            });
            $('.zxWindow .close').click(function (e) {
                $('.zxWindow').fadeOut('fast');
            });
            //
            mq = window.matchMedia("(max-width:640px)");
            mq.addListener(function (changed) {
                changeSize640(changed.matches);
            });
            changeSize640(mq.matches);
            /*左侧菜单*/
            $('.info_tabs li').each(function (index, element) {
                var _el = $(element);
                var _fsm = _el.find('div');
                if (_el.hasClass('bg') && _fsm) {
                    if (isMinStage) {
                        $(_fsm[0]).show();
                    } else {
                        $(_fsm[0]).slideDown('fast');
                    }
                    nowShowLeftMenu = _el;
                    $('.info_tabs li').addClass('hide');
                }
                _el.click(function (e) {
                    if (!$(this).hasClass('bg')) {
                        if (nowShowLeftMenu) {
                            try {
                                if (isMinStage) {
                                    $(nowShowLeftMenu.find('div')[0]).hide();
                                } else {
                                    $(nowShowLeftMenu.find('div')[0]).slideUp('fast');
                                }
                            } catch (e) {
                            }
                            ;
                            nowShowLeftMenu.removeClass();
                        }
                        try {
                            if (isMinStage) {
                                $($(this).find('div')[0]).show();
                            } else {
                                $($(this).find('div')[0]).slideDown('fast');
                            }
                        } catch (e) {
                        }
                        ;
                        $('.info_tabs li').addClass('hide');
                        $(this).addClass('bg');
                        nowShowLeftMenu = $(this);
                    }
                });
            });
            $('.info_tabs .smenu span').click(function (e) {
                var _sm = $(this).parent();
                var _lid = _sm.parent();
                var _uld = _lid.parent();
                _uld.find('li').removeClass('hide');
                _sm.hide();
                if (nowShowLeftMenu) {
                    try {
                        $(nowShowLeftMenu.find('div')[0]).hide();
                    } catch (e) {
                    }
                    ;
                    nowShowLeftMenu.removeClass();
                }
                nowShowLeftMenu = null;
                return false;
            });
            //
            $('.about_top .item').each(function (index, element) {
                $($(element).find('p')[0]).click(function (e) {
                    if (isMinStage) {
                        $($(this).parent().find('ul')[0]).slideToggle('fast');
                    }
                });
            });
        });
        function changeSize640(b) {
            isMinStage = b;
            if (b) {
                $('.about_top .item').each(function (index, element) {
                    $($(element).find('ul')[0]).hide();
                });
            } else {
                $('.about_top .item').each(function (index, element) {
                    $($(element).find('ul')[0]).show();
                });
            }
        }
        function showZx(num) {
            if (zxlist) {
                $('.zxWindow').fadeIn('fast');
                zxlist.slideTo(num);
            } else {
                $('.zxWindow').show();
                zxlist = new Swiper('.zxWindow .swiper-container', {
                    loop: true,
                    pagination: '.zxWindow .swiper-pagination',
                    nextButton: '.zxWindow .swiper-button-next',
                    prevButton: '.zxWindow .swiper-button-prev',
                    initialSlide: num
                });
            }
        }
    </script>
@endsection
