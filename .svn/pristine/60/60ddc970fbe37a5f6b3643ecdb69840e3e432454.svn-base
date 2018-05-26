<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\DataSource;
use App\Models\Domain;
use App\Models\Member;
use Exception;
use Gate;
use Illuminate\Support\Facades\Hash;
use Request;
use Response;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->member = Member::getMember();
    }

    public function index()
    {
        if (Gate::denies('@member')) {
            $this->middleware('deny403');
        }

        return view('admin.members.index');
    }

    public function create()
    {
        if (Gate::denies('@member-create')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        return view('admin.members.create');
    }

    public function store(MemberRequest $request)
    {
        $input = Request::all();
        $nick_name = $input['nick_name'];
        $member_name = $input['name'];
        $password = $input['password'];
        $ip = Request::getClientIp();

        $member = Member::where('name', $member_name)->first();
        if ($member) {
            \Session::flash('flash_error', '用户名已经存在');
            return redirect()->back()->withInput();
        }

        try {
            $salt = str_rand();

            $member = Member::create([
                'name' => $member_name,
                'password' => md5(md5($password) . $salt),
                'nick_name' => $nick_name,
                'mobile' => $input['mobile'],
                'avatar_url' => $input['avatar_url'],
                'type' => $input['type'],
                'state' => Member::STATE_ENABLED,
                'points' => 0,
                'ip' => $ip,
            ]);
            $member->token = \JWTAuth::fromUser($member);
            $member->save();

            \Session::flash('flash_success', '添加成功');
            return redirect('/admin/members');
        } catch (Exception $e) {
            return false;
        }

    }

    public function edit($id)
    {
        if (Gate::denies('@member-edit')) {
            \Session::flash('flash_warning', '无此操作权限');
            return redirect()->back();
        }

        $member = Member::find($id);

        if ($member == null) {
            \Session::flash('flash_warning', '无此记录');
            return redirect('/admin/members');
        }

        return view('admin.members.edit', compact('member'));
    }

    public function update($id)
    {
        $member = Member::find($id);

        $input = Request::all();

        $member->avatar_url = $input['avatar_url'];
        $member->name = $input['name'];
        $member->nick_name = $input['nick_name'];
        $member->title = $input['title'];
        if(isset($input['sex'])){
            $member->sex        = $input['sex'];
        }
        if(isset($input['new_password'])){
            $salt = str_rand();
            $member->password = md5(md5($input['new_password']) . $salt);
            $member->salt = $salt;
        }
        if(isset($input['email'])){
            $member->email      = $input['email'];
        }

        $member->save();

        return redirect('/admin/members');
    }

    public function save($id)
    {
        $input = Request::all();

        $member = Member::find($id);

        $member->avatar_url = $input['avatar_url'];
        $member->name = $input['name'];
        $member->title = $input['title'];
        $member->sex        = $input['sex'];
        $member->email      = $input['email'];

        $member->save();

        return redirect('/member');
    }

    public function message($member_id)
    {
        return view('admin.members.message', compact('member_id'));
    }

    public function table()
    {
        $filters = [
            'id' => Request::has('id') ? intval(Request::get('id')) : 0,
            'name' => Request::has('name') ? trim(Request::get('username')) : '',
            'mobile' => Request::has('mobile') ? trim(Request::get('mobile')) : '',
            'state' => Request::has('state') ? Request::get('state') : '',
        ];

        $offset = Request::get('offset') ? Request::get('offset') : 0;
        $limit = Request::get('limit') ? Request::get('limit') : 20;


        $members = Member::filter($filters)
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();

        $total = Member::filter($filters)
            ->count();


        $members->transform(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'nick_name' => $member->nick_name,
                'points' => $member->points,
                'mobile' => $member->mobile,
                'avatar_url' => $member->avatar_url,
                'ip' => $member->ip,
                'is_certified' => $member->is_certified,
                'type_name' => $member->typeName(),
                'state_name' => $member->stateName(),
                'signed_at' => $member->signed_at,
                'created_at' => empty($member->created_at) ? '' : $member->created_at->toDateTimeString(),
                'updated_at' => empty($member->updated_at) ? '' : $member->updated_at->toDateTimeString(),
            ];
        });
        $ds = New DataSource();
        $ds->total = $total;
        $ds->rows = $members;

        return Response::json($ds);
    }

    public function state($id)
    {
        $member = Member::find($id);
        if ($member->state == Member::STATE_ENABLED) {
            $member->state = Member::STATE_DISABLED;
            $member->save();
            \Session::flash('flash_success', '禁用成功');
        } else {
            $member->state = Member::STATE_ENABLED;
            $member->save();
            \Session::flash('flash_success', '启用成功');
        }
    }

    public function phoneLogin(Domain $domain)
    {
        if (empty($domain->site)) {
            return abort(501);
        }

        return view('themes.' . $domain->theme->name . '.phone.login');
    }

    public function reset()
    {
        $input = Request::all();

        $member = Member::getMember();

        if(!Hash::check($input['oldpass'], $member->password)){
            return $this->responseError('您输入的原密码不正确，请重新输入');
        }

        if($input['password'] !== $input['password2']){
            return $this->responseError('前后输入的密码不一致，请重新输入');
        }

        if (bcrypt($input['password']) == $member->password){
            return $this->responseError('请勿使用旧密码重置密码');
        }

        $input['password'] = bcrypt($input['password']);
        $res = $member->update($input);

        if($res){
            return $this->responseSuccess($res);
        }

    }

    public function verify(Request $request)
    {
        $mobile = Request::get('mobile');
        $captcha = Request::get('captcha');

        try {
            $member = Member::getMember();
            if (!$member) {
                return $this->responseError('登录已过期,请重新登录', 401);
            }
        } catch (Exception $e) {
            return $this->responseError('登录已过期,请重新登录', 401);
        }

        try {
            if (!preg_match("/1[34578]{1}\d{9}$/", $mobile)) {
                throw new Exception('请输入正确的手机号', -1);
            }

            if ($mobile !== $member->mobile) {
                throw new Exception('非用户绑定手机，请用绑定手机操作', -1);
            }

            //比较验证码
            if (!Member::verify($mobile, $captcha)) {
                throw new Exception('手机验证码错误', -1);
            }else{
                //移除验证码
                return $this->responseSuccess();
            }

        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

}
