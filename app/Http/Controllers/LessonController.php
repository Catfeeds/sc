<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Events\UserLogEvent;
use App\Models\UserLog;
use Gate;
use Request;
use Auth;


/**
 * 课时
 */
class LessonController extends Controller
{
    protected $base_url = '/admin/lessons';
    protected $view_path = 'admin.lessons';

    public function __construct()
    {
    }

    //添加课时
    public function create($id)
    {
        if (Gate::denies('@course-create')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $chapter = Chapter::where('parent_id', 0)
            ->where('course_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($chapter) {
            $chapter_id = $chapter['id'];
            $sort = Lesson::where('chapter_id', $chapter_id)->orderBy('sort', 'desc')->first()['sort'];
            $seq = Lesson::where('chapter_id', $chapter_id)->orderBy('seq', 'desc')->first()['seq'];

            if (empty($seq)) {
                $seq = 0;
                $sort = 0;
            }

            $course = Course::find($id)->increment('lesson_num');

            $input = Request::input();
            $input['user_id'] = Auth::user()->id;
            $input['duration'] *= 60;
            $input['chapter_id'] = $chapter_id;
            $input['sort'] = $sort + 1;
            $input['seq'] = $seq + 1;

            $lesson = Lesson::stores($input);

            event(new UserLogEvent(UserLog::ACTION_CREATE . '课时', $chapter_id, Lesson::MODEL_CLASS));
            \Session::flash('flash_success', '添加成功');
        } else {
            \Session::flash('flash_warning', '没有章节，添加失败');
        }

        return redirect('/admin/courses/' . $id . '/manage');
    }

    public function update($id)
    {
        $input = Request::all();
        $input['duration'] *= 60;
        $lesson = Lesson::updates($id, $input);

        \Session::flash('flash_success', '修改成功!');
        event(new UserLogEvent(UserLog::ACTION_UPDATE . '课时', $id, Lesson::MODEL_CLASS));

        return redirect('/admin/courses/' . $input['course_id'] . '/manage');
    }

    public function destroy($id)
    {
        $lesson = Lesson::find($id);
        $course_id = Chapter::find($lesson->chapter_id)->course_id;

        if ($lesson == null) {
            \Session::flash('flash_warning', '无此记录');
            return;
        }

        $seq = Lesson::where('id', '>', $id);
        if ($seq) {
            $seq->decrement('seq');
        }

        $course = Course::find($course_id);
        $course->decrement('lesson_num');

        $lesson->delete();

        \Session::flash('flash_success', '删除成功');
        event(new UserLogEvent(UserLog::ACTION_DELETE . '课时', $id, Lesson::MODEL_CLASS));

    }

}
