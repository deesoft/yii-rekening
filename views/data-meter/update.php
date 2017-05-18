<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DataMeter */

$this->title = 'Update Data Meter: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Data Meters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="data-meter-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
