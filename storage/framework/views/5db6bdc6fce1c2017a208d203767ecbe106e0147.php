<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title><?php echo e(env('APP_NAME')); ?></title>
    <link href="/plugins/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
    <script src="/plugins/jquery/2.2.4/jquery.min.js"></script>
    <link href="/css/admin/login.css" rel="stylesheet">
</head>
<body class="pace-top">
<!-- begin #page-container -->
<div id="page-container" class="login_box">
    <!-- begin login -->
    <form role="form" method="POST" action="<?php echo e(url('admin/login')); ?>">
        <div class="login_iptbox">

            <button type="submit" class="login_tj_btn">　</button>
            用户名：<input type="text" name="user_id"
                       value="<?php echo e(session('_old_input') ? session('_old_input.user_id') : ''); ?>" class="ipt"
                       placeholder=""/>
            <?php if($errors->has('user_id') || session('cancel')): ?>
                <span class="error-block">*</span>
            <?php endif; ?>

            密 码：<input type="password" name="password"
                       value="<?php echo e(session('_old_input') ? session('_old_input.password') : ''); ?>" class="ipt"
                       placeholder=""/>
            <?php if($errors->has('password')): ?>
                <span class="error-block">*</span>
            <?php endif; ?>


            验证码：<input type="text" name="captcha" class="form-control ipt input_captcha"
                       id="input_captcha" title="点击更换验证码" maxlength="5"
                       placeholder=""/>
            <?php if($errors->has('captcha')): ?>
                <span class="error-block">*</span>
            <?php endif; ?>
            <div class="captcha_area" id="captcha_area" tabindex='3' onclick="refreshCaptcha('login_captcha')"
                 style="right: <?php echo $errors->has('user_id') ? '' : '112px'; ?>;">
                <img src="<?php echo e(captcha_src()); ?>" id="login_captcha" alt="验证码"
                     title="点击刷新图片">
                <span class="click_captcha">点击更换验证码</span>
            </div>


        </div>
    </form>
</div>
</body>
</html>

<script>
    function refreshCaptcha(id_name) {
        var input_captcha = $('#input_captcha').val();
        var img_src = "<?php echo e(url('captcha')); ?>" + '?id_name=' + id_name + '&t=' + Math.random();
        $('#' + id_name).prop('src', img_src);
    }

    $('#input_captcha').on('focus', this, function () {
        $('#captcha_area').show();
    })
</script>