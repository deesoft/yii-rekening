<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DataMeter */

$this->title = 'Create Data Meter';
$this->params['breadcrumbs'][] = ['label' => 'Data Meters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-meter-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
