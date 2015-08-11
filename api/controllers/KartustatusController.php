<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\Barang;
use app\models\Penjualan;
use app\models\PenjualanDet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class KartustatusController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (isset($this->actions['*'])) {
            $verbs = $this->actions['*'];
        } else {
            return $event->isValid;
        }
        $verb = Yii::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $verbs);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionIndex() {
        $params = json_decode(file_get_contents("php://input"), true);

        $query = new Query;
        $query->from('penjualan')
                ->select('*')
                ->where('customer_id = "' . $params['customer']['id'] . '"')
                ->andFilterWhere(['between', 'tanggal', $params['tanggal']['startDate'], $params['tanggal']['endDate']]);
        $command = $query->createCommand();
        $models = $command->queryAll();


        $data = array();
        $i = 0;
        foreach ($models as $val => $key) {
            $data[$val] = $key;

            $query2 = new Query;
            $query2->from('penjualan_det, m_produk')
                    ->select('m_produk.nama')
                    ->where('penjualan_det.produk_id = m_produk.id and penjualan_det.penjualan_id = "' . $key['id'] . '"');
            $command2 = $query2->createCommand();
            $models2 = $command2->queryAll();
            $terapi = array();
            foreach ($models2 as $v) {
                $terapi[] = $v['nama'];
            }
            $data[$i]['terapi'] = join(",", $terapi);
            unset($terapi);
            $i++;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($data)), JSON_PRETTY_PRINT);
    }

    private function setHeader($status) {

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
    }

    private function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}

?>
