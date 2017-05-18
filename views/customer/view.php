<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tabs = [
    [
        'label' => 'Invoice',
        'page' => null,
        'view' => 'invoice'
    ],
    [
        'label' => 'Usage',
        'page' => 'usage',
        'view' => 'usage'
    ],
];
?>
<div class="customer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-4">
            <p>
                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?=
                Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ])
                ?>
            </p>

            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'code',
                    'name',
                    'address.address',
                ],
            ])
            ?>
        </div>
        <div class="col-lg-8">
            <ul class="nav nav-tabs" role="tablist">
                <?php foreach ($tabs as $tab): ?>
                    <?php if ($page === $tab['page']): ?>
                        <li role="presentation" class="active"><?= Html::a($tab['label'])?></li>
                        <?php $viewFile = $tab['view']; ?>
                    <?php else: ?>
                        <li role="presentation"><?= Html::a($tab['label'], ['view', 'id' => $model->id, 'page' => $tab['page']]) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <?= $this->render($viewFile, ['model'=>$model])?>
                </div>
            </div>
        </div>
    </div>
</div>
