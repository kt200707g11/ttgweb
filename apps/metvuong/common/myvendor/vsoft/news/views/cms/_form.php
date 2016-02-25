<?php

use vsoft\news\models\CmsCatalog;
use vsoft\news\models\Status;
use vsoft\user\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model funson86\cms\models\CmsShow */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="cms-show-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-11\">{input}</div><div class=\"col-lg-1\"></div>\n<div class=\"col-lg-11\">{hint}{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?php
    $parentCatalog = ArrayHelper::merge([20 => 'Homepage', 2 => 'News'], ArrayHelper::map(CmsCatalog::get(Yii::$app->params['newsCatID'], CmsCatalog::find()->where(['status' => Status::STATUS_ACTIVE])->asArray()->all()), 'id', 'label'));
//    $catalog_data = ArrayHelper::map(CmsCatalog::find()->where(['status' => Status::STATUS_ACTIVE])->all(), 'id', 'title');
    $cat_id = $model->catalog_id > 0 ? $model->catalog_id : Yii::$app->params['newsCatID'];
    echo $form->field($model, 'catalog_id')->dropDownList($parentCatalog, [
        'options' => [$cat_id => ['Selected ' => true]],
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brief')->textarea(['raw'=>4]) ?>

    <?= $form->field($model, 'content')->widget(\common\widgets\CKEditor::className(), [
        'editorOptions' => [
            'preset' => 'full',
            'inline' => false,
        ],
    ]); ?>

    <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>

    <?= $model->isNewRecord ? $form->field($model, 'banner')->widget(\kartik\file\FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'previewFileType' => 'image',
            'showUpload' => false,
            'browseLabel' => '',
            'removeLabel' => '',
            'mainClass' => 'input-group-lg'
        ]
    ]) : $form->field($model, 'banner')->widget(\kartik\file\FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'previewFileType' => 'image',
            'showUpload' => false,
            'browseLabel' => '',
            'removeLabel' => '',
            'initialPreview' => [
                Html::img("/store/news/show/" . $model->banner, ['class' => 'file-preview-image', 'alt' => 'Banner', 'title' => $model->banner]),
            ],
            'initialCaption' => $model->banner,
//            'overwriteInitial'=>false, // used in multi upload case
            'mainClass' => 'input-group-lg'
        ]]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'readOnly' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(\vsoft\news\models\Status::labels()) ?>

    <div class="form-group">
        <label class="col-lg-1 control-label"></label>
        <div class="col-lg-11">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), 'index', ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
