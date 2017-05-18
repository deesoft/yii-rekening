<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this View */
$opts = json_encode([
    'getCustomerUrl' => Url::to('get-customers'),
    'postMeterUrl' => Url::to('post-meter'),
]);
$this->registerJs("var opts = $opts;");
$this->registerJs($this->render('inputs.js'));
?>
<div class="inputs-data-meter">
    <div class="form-group field-datameter-month required">
        <label for="datameter-month" class="control-label">Month</label>
        <?= Html::dropDownList('month', '', $months, ['class' => 'form-control', 'id' => 'datameter-month']) ?>
    </div>
    <div class="form-group field-datameter-address required">
        <label for="datameter-address" class="control-label">Address</label>
        <?= Html::dropDownList('address', '', $addresses, ['class' => 'form-control', 'id' => 'datameter-address', 'prompt' => '--']) ?>
    </div>
</div>
<div>
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Previous Meter</th>
                <th>Input</th>
            </tr>
            <tr style="display: none;" id="row-template">
                <td class="code"></td>
                <td class="name"></td>
                <td class="previous"></td>
                <td class="input"><input class="form-control"></td>
            </tr>
        </thead>
        <tbody id="tbl-body">
            
        </tbody>
    </table>
</div>