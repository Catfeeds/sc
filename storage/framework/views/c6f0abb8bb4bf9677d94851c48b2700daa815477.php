<div class="modal fade common" id="<?php echo e($id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
                <h4 class="modal-title" id="modal_title"></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="window_msg" style="display: none;">
                    <p></p>
                </div>
                <div id="contents">
                    <?php echo $__env->yieldContent('handle'); ?>
                </div>
            </div>
        </div>
    </div>
</div>