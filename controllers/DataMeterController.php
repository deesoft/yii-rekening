<?php

namespace app\controllers;

use Yii;
use app\models\DataMeter;
use app\models\DataMeterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Periode;
use app\models\Address;
use app\models\Customer;
use yii\helpers\ArrayHelper;

/**
 * DataMeterController implements the CRUD actions for DataMeter model.
 */
class DataMeterController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'post-meter' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all DataMeter models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DataMeterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->with('customer');
        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    public function actionInputs()
    {
        $months = Periode::find()->orderBy(['date_from' => SORT_DESC])->limit(3)->all();
        if (empty($months)) {
            return $this->redirect('periode/create');
        }
        $months = ArrayHelper::map($months, 'id', 'name');

        $addresses = ArrayHelper::map(Address::find()->all(), 'id', 'address');
        return $this->render('inputs', [
                'months' => $months,
                'addresses' => $addresses
        ]);
    }

    public function actionGetCustomers($periode, $address)
    {
        $prev_periode = Periode::find()->where(['<', 'id', $periode])->max('id');
        Yii::$app->getResponse()->format = 'json';
        $rows = Customer::find()->alias('c')
            ->select(['c.*', 'meter1' => 'd1.meter', 'meter2' => 'd2.meter'])
            ->leftJoin('{{%data_meter}} d1', '{{d1}}.[[customer_id]]={{c}}.[[id]] and {{d1}}.[[periode_id]]=:p1', ['p1' => $prev_periode])
            ->leftJoin('{{%data_meter}} d2', '{{d2}}.[[customer_id]]={{c}}.[[id]] and {{d2}}.[[periode_id]]=:p2', ['p2' => $periode])
            ->where(['c.address_id' => $address, 'status' => 1])
            ->asArray()
            ->all();
        return [
            'periode' => $periode,
            'rows' => $rows,
        ];
    }

    public function actionPostMeter()
    {
        $request = Yii::$app->getRequest();
        Yii::$app->getResponse()->format = 'json';
        $periode = $request->post('periode');
        $customer_id = $request->post('c_id');
        if (empty($periode) || empty($customer_id)) {
            throw new \yii\base\InvalidParamException('');
        }
        $model = DataMeter::findOne(['periode_id' => $periode, 'customer_id' => $customer_id]);
        if ($model === null) {
            $model = new DataMeter([
                'periode_id' => $periode,
                'customer_id' => $customer_id
            ]);
        }
        $model->meter = $request->post('value');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->save() && ($invoice=$model->createInvoice())!==false) {
                $transaction->commit();
                $result = $invoice->component;
                $result['current_meter'] = $model->meter;
                return $result;
            }
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        throw new \yii\base\InvalidParamException('');
    }

    /**
     * Displays a single DataMeter model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
                'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DataMeter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DataMeter();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                    'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DataMeter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                    'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing DataMeter model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DataMeter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DataMeter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DataMeter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
