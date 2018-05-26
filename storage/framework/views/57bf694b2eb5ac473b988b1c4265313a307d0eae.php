<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">菜单导航</li>
                        

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-edit"></i>
                    <span class="menu-item-top">内容管理</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('edit-school')): ?>
                    <li>
                        <a href="/admin/schools">
                            <i class="fa fa-image"></i>
                            <span class="menu-item-top">学校管理</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('edit-grade')): ?>
                    <li>
                        <a href="/admin/grades">
                            <i class="fa fa-file-o"></i>
                            <span class="menu-item-top">班级管理</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('edit-student')): ?>
                    <li>
                        <a href="/admin/students">
                            <i class="fa fa-calendar-check-o"></i>
                            <span class="menu-item-top">学生管理</span>
                        </a>
                    </li>
                    <?php endif; ?>
                        
                    <li>
                        <a href="/admin/self">
                            <i class="fa fa-calendar-check-o"></i>
                            <span class="menu-item-top">个人管理</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-calendar"></i>
                    <span class="menu-item-top">日志查询</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="/admin/users/logs">
                            <i class="fa fa-user-o"></i>
                            <span class="menu-item-top">操作日志</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/push/logs">
                            <i class="fa fa-envelope-o"></i>
                            <span class="menu-item-top">推送日志</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/sms/logs">
                            <i class="fa fa-commenting-o"></i>
                            <span class="menu-item-top">短信日志</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/system/logs">
                            <i class="fa fa-envelope"></i>
                            <span class="menu-item-top">系统日志</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cog"></i>
                    <span class="menu-item-top">系统管理</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('@option')): ?>
                        <li><a href="/admin/options"><i class="fa fa-cog"></i> 系统设置</a></li>
                    <?php endif; ?>
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('@dictionary')): ?>
                        <li><a href="/admin/dictionaries"><i class="fa fa-book"></i> 字典设置</a></li>
                    <?php endif; ?>
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('@app')): ?>
                        <li><a href="/admin/apps"><i class="fa fa-android"></i> 版本管理</a></li>
                    <?php endif; ?>
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('@module')): ?>
                        <li><a href="/admin/modules"><i class="fa fa-cubes"></i> 模块管理</a></li>
                    <?php endif; ?>
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('@menu')): ?>
                        <li><a href="/admin/menus"><i class="fa fa-bars"></i> 菜单管理</a></li>
                    <?php endif; ?>
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('@role')): ?>
                        <li><a href="/admin/roles"><i class="fa fa-street-view"></i> 角色管理</a></li>
                    <?php endif; ?>
                    <?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('@user')): ?>
                        <li><a href="/admin/users"><i class="fa fa-user"></i> 用户管理</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<script>
    $(document).ready(function () {
        var url = window.location.pathname;
        url = url.replace(/\/[0-9]*\/edit/, ''); // articles/1/edit
        url = url.replace(/\b\/create[\/0-9]*\b/, ''); // categories/create/1
        url = url.replace(/\b\/create\?[\s\S]*\b/, ''); // articles/create?category_id=1
        $('ul.treeview-menu>li').find('a[href="' + url + '"]').closest('li').addClass('active');  //二级链接高亮
        $('ul.treeview-menu>li').find('a[href="' + url + '"]').closest('li.treeview').addClass('active');  //一级链接高亮
        $('ul.treeview-menu>li').find('a[href="' + url + '"]').parent().addClass('active'); //单独一级高亮
    });
</script>
