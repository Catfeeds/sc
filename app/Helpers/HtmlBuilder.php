<?php

namespace App\Helpers;


use Gate;

class HtmlBuilder
{
    /**
     * 生成菜单编辑器代码
     *
     * @param $menus
     * @return string
     */
    public static function menuEditor($menus)
    {
        $html = '<ol class="dd-list">';
        foreach ($menus as $menu) {
            $html .= '<li class="dd-item dd3-item" data-id="' . $menu->id . '">';
            $html .= '    <div class="dd-handle dd3-handle"></div>';
            $html .= '    <div class="dd3-content"><i class="fa ' . $menu->icon . '"></i> ' . $menu->name;
            $html .= '        <button class="btn btn-xs btn-danger pull-right btn-menu-remove" data-id="' . $menu->id . '"><i class="fa fa-times"></i></button>';
            $html .= '        <button class="btn btn-xs btn-success pull-right btn-menu-edit" data-id="' . $menu->id . '" data-name="' . $menu->name . '" data-icon="' . $menu->icon . '" data-url="' . $menu->url . '" data-permission="' . $menu->permission .'"><i class="fa fa-edit"></i></button>';
            $html .= '    </div>';

            if (count($menu->children) > 0) {
                $html .= static::menuEditor($menu->children()->orderBy('sort')->get());
            }

            $html .= '</li>';
        }
        $html .= '</ol>';

        return $html;
    }


    /**
     * 生成后台课程-课时管理菜单编辑器代码
     * @param $menus
     * @return string
     */
    public static function chapterEditor($chapterList)
    {
        $html = '<ol class="dd-list">';
        foreach ($chapterList as $chapter) {
            if (!isset($chapter->name)) {
                $chapter->name = $chapter->title;
            }
            if (!isset($chapter->parent_id)) {
                $chapter->parent_id = $chapter->chapter_id;
            }

            $html .= '<li class="dd-item dd3-item" data-id="' . $chapter->id . '">';
            $html .= '    <div class="dd-handle dd3-handle dd3-handle-seq"><i>' . $chapter->seq . '</i></div>';
            $html .= '    <div class="dd3-content dd3-content-name"><i class="fa ' . $chapter->icon . '"></i><b>' . $chapter->name . '</b>';

            if ($chapter->parent_id == 0) {
                $html .= '        <button class="btn btn-xs btn-danger pull-right btn-menu-remove" data-id="' . $chapter->id . '"><i class="fa fa-times"></i></button>';
                $html .= '        <button class="btn btn-xs btn-success pull-right btn-menu-edit" data-id="' . $chapter->id . '" data-name="' . $chapter->name . '" data-course_id="' . $chapter->course_id . '"><i class="fa fa-edit"></i></button>';
            } else {
                $html .= '        <button class="btn btn-xs btn-danger pull-right btn-menu-remove-lesson" data-id="' . $chapter->id . '"><i class="fa fa-times"></i></button>';
                $html .= '        <button class="btn btn-xs btn-success pull-right btn-menu-edit-lesson" data-id="' . $chapter->id . '" data-name="' . $chapter->name . '" data-type="' . $chapter->type . '" data-intro="' . $chapter->intro . '" data-content="' . $chapter->content . '" data-duration="' . $chapter->duration / 60 . '" data-media_url="' . $chapter->media_url . '" data-free="' . $chapter->free . '" data-course_id="' . $chapter->chapter()->first()->course_id . '"><i class="fa fa-edit"></i></button>';
            }

            $html .= '    </div>';

            if (count($chapter->lessons) > 0) {
                $html .= static::chapterEditor($chapter->lessons()->orderBy('seq')->get());
            }

            $html .= '</li>';
        }
        $html .= '</ol>';

        return $html;
    }

    /**
     * 生成菜单树代码
     *
     * @param $menus
     * @return string
     */
    public static function menuTree($menus)
    {
        $html = '';
        foreach ($menus as $menu) {
            if (empty($menu->permission) || Gate::allows($menu->permission)) {
                if (count($menu->children)) {
                    $html .= '<li class="treeview">';
                } else {
                    $html .= '<li>';
                }
                $html .= '    <a href="' . $menu->url . '">';
                $html .= '        <i class="fa ' . $menu->icon . '"></i>';
                $html .= '        <span class="menu-item-top">' . $menu->name . '</span>';
                if (count($menu->children)) {
                    $html .= '        <i class="fa fa-angle-left pull-right"></i>';
                }
                $html .= '    </a>';
                if (count($menu->children)) {
                    $html .= '<ul class="treeview-menu">';
                    $html .= static::menuTree($menu->children()->orderBy('sort')->get());
                    $html .= '</ul>';
                }
                $html .= '</li>';
            }
        }
        return $html;
    }
}