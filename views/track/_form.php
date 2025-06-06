<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Track $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="track-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'track_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'new' => 'New', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'canceled' => 'Canceled', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
