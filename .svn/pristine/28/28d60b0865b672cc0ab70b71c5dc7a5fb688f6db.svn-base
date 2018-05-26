@extends('admin.layouts.master')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                教师认证
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">教师认证</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-info">
                        <div class="box-body">
                            @include('admin.layouts.flash')
                            @include('admin.layouts.modal', ['id' => 'modal_certify'])
                            <div class="btn-group margin-bottom pull-right">
                                <input type="hidden" name="state" id="state" value=""/>
                                <button type="button" class="btn btn-info btn-xs margin-r-5 filter"
                                        data-active="btn-info" id="" value="">全部
                                </button>
                                <button type="button" class="btn btn-default btn-xs margin-r-5 filter"
                                        data-active="btn-primary"
                                        value="{{ \App\Models\Certification::STATE_CERTIFING }}">待审核
                                </button>
                                <button type="button" class="btn btn-default btn-xs margin-r-5 filter"
                                        data-active="btn-success"
                                        value="{{ \App\Models\Certification::STATE_SUCCESS }}">审核通过
                                </button>
                                <button type="button" class="btn btn-default btn-xs margin-r-5 filter"
                                        data-active="btn-danger"
                                        value="{{ \App\Models\Certification::STATE_FAILURE }}">拒绝认证
                                </button>
                            </div>
                            <div class="cb-toolbar"></div>
                            <table id="table" data-toggle="table">
                                <thead>
                                <tr>
                                    <th data-field="id" data-width="90" data-align="center">ID</th>
                                    <th data-field="member_id" data-width="120" data-align="center">教师id</th>
                                    <th data-field="mobile" data-width="120" data-align="center">手机号</th>
                                    <th data-field="name" data-width="120" data-align="center" data-align="center">
                                        真实姓名
                                    </th>
                                    <th data-field="title" data-width="120" data-align="center">职称</th>
                                    <th data-field="degree" data-width="150" data-align="center">学历</th>
                                    <th data-field="card_photo" data-align="center" data-formatter="photoFormatter"
                                        data-width="290">
                                        手持身份证照片
                                    </th>
                                    <th data-field="state_name" data-align="center" data-width="70"
                                        data-formatter="stateFormatter">状态
                                    </th>
                                    <th data-field="user_name" data-width="120" data-align="center">操作人</th>
                                    <th data-field="created_at" data-width="120" data-align="center">申请时间</th>
                                    <th data-field="action" data-align="center" data-width="100"
                                        data-formatter="actionFormatter"
                                        data-events="actionEvents">操作
                                    </th>
                                </tr>
                                </thead>
                            </table>
                            @include('admin.members.script')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection