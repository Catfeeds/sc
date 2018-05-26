<?php

namespace App\Http\Controllers;

use App\Events\UserLogEvent;
use App\Models\UserLog;
use App\Models\Chapter;
use App\Models\Course;
use Gate;
use Request;
use Auth;

/**
 * 章节
 */
class ChapterController extends Controller
{
    protected $base_url = '/admin/chapters';
    protected $view_path = 'admin.chapters';

    public function __construct()
    {
    }

    public function manage($id)
    {
        if (Gate::denies('@course-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $chapterList = Chapter::getChapterList($id);
        $course = Course::find($id);
        return view('admin.courses.chapters', ['chapterList' => $chapterList, 'course_id' => $id, 'content' => $course, 'base_url' => $this->base_url, 'back_url' => $this->base_url . '?category_id=' . $course->category_id]);
    }

    public function create($id)
    {
        $seq = Chapter::where('course_id', $id)->orderBy('seq', 'desc')->first()['seq'];

        if (empty($seq)) {
            $seq = 0;
        }
        $input = Request::input();
        $input['user_id'] = Auth::user()->id;
        $input['seq'] = $seq + 1;
        $input['course_id'] = $id;

        $chapter = Chapter::stores($input);

        event(new UserLogEvent(UserLog::ACTION_CREATE . '章节', $id, Chapter::MODEL_CLASS));

        return redirect('/admin/courses/' . $id . '/manage');
    }

    public function sorts()
    {
        $menus = Request::get('data');
        $menus = json_decode(json_encode($menus));
        Chapter::sorts($menus);
    }

    public function update($id)
    {
        $input = Request::all();
        $chapter = Chapter::updates($id, $input);

        \Session::flash('flash_success', '修改成功!');
        event(new UserLogEvent(UserLog::ACTION_UPDATE . '章节', $id, Chapter::MODEL_CLASS));

        return redirect('/admin/courses/' . $input['course_id'] . '/manage');
    }

    public function destroy($id)
    {
        $chapter = Chapter::find($id);

        if ($chapter == null) {
            \Session::flash('flash_warning', '无此记录');
            return;
        }

        $seq = Chapter::where('id', '>', $id);
        if ($seq) {
            $seq->decrement('seq');
        }

        $chapter->lessons()->delete();
        $chapter->delete();

        \Session::flash('flash_success', '删除成功');
        event(new UserLogEvent(UserLog::ACTION_DELETE . '章节', $id, Chapter::MODEL_CLASS));

    }
}
