<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                编辑栏目
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/categories">分类管理</a></li>
                <li class="active">编辑</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <!-- right column -->
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-info">
                        <div class="box-body">
                            <?php echo $__env->make('admin.errors.list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <?php echo Form::model($category,['method' => 'PATCH', 'action' => ['CategoryController@update', $category->id],'class' => 'form-horizontal']); ?>


                            <?php echo $__env->make('admin.categories.form',['element'=>json_encode([['name'=>'image_url','item'=>'0'],['name'=>'cover_url','item'=>'0']])], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php echo $__env->make('admin.widgets.oss',['module_name'=>'category'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                            <?php echo Form::close(); ?>


                        </div>
                    </div><!-- /.box -->
                </div><!--/.col (right) -->
            </div>   <!-- /.row -->
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>