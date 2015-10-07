<?php
/** @var \dosamigos\fileupload\FileUploadUI $this */
use yii\helpers\Html;
$context = $this->context;
?>
    <!-- The file upload form used as target for the file upload widget -->
<?= Html::beginTag('div', $context->options); ?>
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar">
        <div class="col-lg-7">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span><?= Yii::t('fileupload', 'Chọn ảnh') ?>...</span>

                <?= $context->model instanceof \yii\base\Model && $context->attribute !== null
                    ? Html::activeHiddenInput($context->model, $context->attribute, ['id' => null, 'value' => $context->value])
                    . Html::activeInput('file', $context->model, $context->attribute, $context->fieldOptions)
                    : Html::hiddenInput($context->name, $context->fieldOptions['values'])
                	. Html::fileInput('upload', $context->value, $context->fieldOptions);?>

            </span>
            <a class="btn btn-primary start">
                <i class="glyphicon glyphicon-upload"></i>
                <span><?= Yii::t('fileupload', 'Start upload') ?></span>
            </a>
            <a class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span><?= Yii::t('fileupload', 'Cancel upload') ?></span>
            </a>
            <a class="btn btn-danger delete">
                <i class="glyphicon glyphicon-trash"></i>
                <span><?= Yii::t('fileupload', 'Delete') ?></span>
            </a>
            <input type="checkbox" class="toggle">
            <!-- The global file processing state -->
            <span class="fileupload-process"></span>
        </div>
        <!-- The global progress state -->
        <div class="col-lg-5 fileupload-progress fade">
            <!-- The global progress bar -->
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
            <!-- The extended global progress state -->
            <div class="progress-extended">&nbsp;</div>
        </div>
    </div>
    <!-- The table listing the files available for upload/download -->
    <ul class="files"></ul>
<?= Html::endTag('div');?>