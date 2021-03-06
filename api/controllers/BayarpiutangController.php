<?php

namespace app\controllers;

use Yii;
use app\models\Penjualan;
use app\models\PenjualanDet;
use app\models\Pinjaman;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BayarpiutangController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'cabang' => ['get'],
                    'excel' => ['get'],
                    'customer' => ['get'],
                    'produk' => ['get'],
                    'det_penjualan' => ['get'],
                    'kode' => ['get'],
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
        //init variable
        session_start();
        $params = $_REQUEST;
        $filter = array();
        $sort = "pinjaman.id ASC";
        $offset = 0;
        $limit = 10;

        //limit & offset pagination
        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];

        //sorting
        if (isset($params['sort'])) {
            $sort = $params['sort'];
            if (isset($params['order'])) {
                if ($params['order'] == "false")
                    $sort.=" ASC";
                else
                    $sort.=" DESC";
            }
        }

        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('pinjaman')
                ->join('LEFT JOIN', 'penjualan', 'pinjaman.penjualan_id= penjualan.id')
                ->join('LEFT JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('LEFT JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->join('LEFT JOIN', 'm_user', 'penjualan.created_by= m_user.id')
                ->where('pinjaman.debet != 0')
                ->orderBy($sort)
                ->select("m_user.nama as petugas, pinjaman.created_at, pinjaman.debet, pinjaman.credit as kredit, pinjaman.tanggal_transaksi, pinjaman.status ,m_cabang.nama as klinik, m_customer.nama as customer,penjualan.tanggal as tanggal, penjualan.kode as kode, m_customer.no_tlp as no_tlp,
                            m_customer.email as email, m_customer.alamat as alamat, penjualan.keterangan as keterangan, penjualan.id as penjualan_id");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tanggal') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'tanggal', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {
        $isi = array();
        $findDetail = Pinjaman::find()
                ->where("penjualan_id=" . $id)
                ->orderBy('id DESC')
                ->all();
        foreach ($findDetail as $val) {
            $isi[] = $val->attributes;
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $isi), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $total = 0;
        $params = json_decode(file_get_contents("php://input"), true);
        $noErrors = false;
        $detail = $params['detail'];
        $id = array();
        foreach ($detail as $value) {
            $model = Pinjaman::findOne($value['id']);
            if (empty($model)) {
                $model = new Pinjaman();
            }
            $model->attributes = $value;
            $model->status = $params['form']['status'];
            $model->penjualan_id = $params['form']['penjualan_id'];
            if ($model->save()) {
                $noErrors = true;
            }
            $id[] = $model->id;
        }
        // ganti status sukses
        $ganti = Pinjaman::findAll(['penjualan_id' => $params['form']['penjualan_id']]);
        $debet = 0;
        $credit = 0;
        Yii::error($ganti);
        foreach ($ganti as $data) {
            $debet += $data->debet;
            $credit += $data->credit;
            if ($debet == $credit) {
                $sModel = Pinjaman::findOne($value['id']);
                $sModel->status = 'Lunas';
                $sModel->save();
            }
        }

        $deleteAll = Pinjaman::deleteAll('id NOT IN(' . implode(',', $id) . ') AND penjualan_id=' . $params['form']['penjualan_id']);

        if ($deleteAll) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $id = array();
        foreach ($params['detail'] as $key => $val) {
            $model = Pinjaman::findOne($val['id']);
            if (empty($model)) {
                $model = new Pinjaman();
            }
            $model->attributes = $val;
            $model->status = $params['form']['status'];
            $model->penjualan_id = $params['form']['penjualan_id'];
            $model->tanggal_transaksi = date('Y-m-d', strtotime($val['tanggal_transaksi']));
            $model->save();
            $id[] = $model->id;
        }
        $ganti = Pinjaman::findAll(['penjualan_id' => $params['form']['penjualan_id']]);
        $debet = 0;
        $credit = 0;
        Yii::error($ganti);
        foreach ($ganti as $data) {
            $debet += $data->debet;
            $credit += $data->credit;
            if ($debet == $credit) {
                Pinjaman::updateAll(['status' => 'Lunas'], 'penjualan_id="' . $params['form']['penjualan_id'] . '"');
            }
        }

        $delete = Pinjaman::deleteAll('id NOT IN(' . implode(',', $id) . ')');
    }

    public function actionCustomer() {
        $query = new Query;
        $query->from('m_customer')
                ->select('*')
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'customer' => $models));
    }

    public function actionCabang() {
        $query = new Query;
        $query->from('m_cabang')
                ->select('*')
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'cabang' => $models));
    }

    public function actionKode() {
        $query = new Query;
        $query->from('pinjaman')
                ->join('JOIN', 'penjualan', 'pinjaman.penjualan_id= penjualan.id')
                ->join('JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->where('pinjaman.status !="lunas" and debet is not null')
                ->select("m_cabang.nama as klinik, m_customer.nama as customer, penjualan.kode as kode,penjualan.id as id");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => $models));
    }

    public function actionDet_penjualan($id) {
        $total = 0;
        $query = new Query;
        $query->from('penjualan')
                ->join('JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->where('penjualan.id="' . $id . '"')
                ->select("m_customer.no_tlp as no_tlp, m_customer.email as email, m_customer.alamat as alamat, penjualan.tanggal as tanggal,
                        m_cabang.nama as klinik, penjualan.keterangan as keterangan");
        $command = $query->createCommand();
        $models = $command->queryOne();

        $query2 = new Query;
        $query2->from('pinjaman')
                ->where('penjualan_id="' . $id . '"')
                ->select('*');
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();
        foreach ($detail as $data) {
            $total += $data['credit'];
        }
        $this->setHeader(200);

        echo json_encode(array('penjualan' => $models, 'detail' => $detail, 'total' => $total));
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = PenjualanDet::deleteAll(['penjualan_id' => $id]);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Pinjaman::findOne($id)) !== null) {
            return $model;
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Bad request'), JSON_PRETTY_PRINT);
            exit;
        }
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

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("excel", ['models' => $models]);
    }

}

?>
