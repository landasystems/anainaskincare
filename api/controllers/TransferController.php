<?php

namespace app\controllers;

use Yii;
use app\models\Transfer;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class TransferController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'kirim' => ['get'],
                    'terima' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (excel(isset($this->actions['*']))) {
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

    public function actionTerima() {
        //init variable
        session_start();
        $params = $_REQUEST;
        $filter = array();
        $sort = "transfer.id DESC";
        $offset = 0;
        $limit = 10;
        //        Yii::error($params);
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
                ->from('transfer')
                ->join('LEFT JOIN', 'm_cabang as kirim', 'transfer.gudang_id = kirim.id')
                ->join('LEFT JOIN', 'm_cabang as terima', 'transfer.cabang_id = terima.id')
                ->join('LEFT JOIN', 'm_user', 'transfer.created_by = m_user.id')
                ->join('LEFT JOIN', 'm_user as penerima', 'transfer.penerima_id = penerima.id')
                ->orderBy($sort)
                ->select("transfer.*, kirim.nama as gudang, penerima.nama as penerima, terima.nama as cabang, m_user.nama as pengirim");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        $query->andFilterWhere(['in', 'cabang_id', $_SESSION['user']['cabang_id']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionKirim() {
        //init variable
        session_start();
        $params = $_REQUEST;
        $filter = array();
        $sort = "transfer.id DESC";
        $offset = 0;
        $limit = 10;
        //        Yii::error($params);
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
                ->from('transfer')
                ->join('LEFT JOIN', 'm_cabang as kirim', 'transfer.gudang_id = kirim.id')
                ->join('LEFT JOIN', 'm_cabang as terima', 'transfer.cabang_id = terima.id')
                ->join('LEFT JOIN', 'm_user', 'transfer.created_by = m_user.id')
                ->orderBy($sort)
                ->select("transfer.*, kirim.nama as gudang, terima.nama as cabang, m_user.nama as pengirim");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        $query->andFilterWhere(['in', 'gudang_id', $_SESSION['user']['cabang_id']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = Transfer::findOne($id);

        $query = new Query;
        $query->select('transfer_det.*, m_produk.nama, m_produk.id')
                ->from('transfer_det')
                ->join('LEFT JOIN', 'm_produk', 'transfer_det.id_barang = m_produk.id')
                ->where('transfer_det.transfer_id=' . $id)
                ->all();
        $command = $query->createCommand();
        $models = $command->queryAll();

        $data = array();
        $data['id'] = $model->id;
        $data['status'] = $model->status;
        $data['nota'] = $model->nota;
        $data['tgl_transfer'] = $model->tgl_transfer;
        $data['tgl_terima'] = $model->tgl_terima;
        $data['gudang'] = array('nama' => isset($model->gudang->nama) ? $model->gudang->nama : '', 'id' => isset($model->gudang->id) ? $model->gudang->id : '');
        $data['tujuan'] = array('nama' => isset($model->cabang->nama) ? $model->cabang->nama : '', 'id' => isset($model->cabang->id) ? $model->cabang->id : '');
        $data['konfirmasi'] = isset($model->konfirmasi->nama) ? $model->konfirmasi->nama : '';


        $det = array();
        foreach ($models as $key => $val) {
            $det[$key]['produk'] = array('nama' => $val['nama'], 'id' => $val['id']);
            $det[$key]['jumlah'] = $val['qty'];
            $det[$key]['harga'] = $val['harga'];
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'details' => $det), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        session_start();
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Transfer;
        $model->attributes = $params['form'];
        $model->gudang_id = $params['form']['gudang']['id'];
        $model->cabang_id = $params['form']['tujuan']['id'];
        $model->pengirim_id = $_SESSION['user']['id'];
        $model->status = 'pending';

        if ($model->save()) {
            if (isset($params['detail']) and ! empty($params['detail'])) {
                foreach ($params['detail'] as $val) {
                    $det = new \app\models\TransferDet();
                    $det->id_barang = $val['produk']['id'];
                    $det->transfer_id = $model->id;
                    $det->qty = $val['jumlah'];
                    $det->harga = $val['harga'];
                    $det->save();
                }
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        session_start();
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);

        if (isset($params['status'])) {
            $model->status = $params['status'];
            $model->tgl_terima = date("Y-m-d H:i:s");
            $model->penerima_id = $_SESSION['user']['id'];
        } else {
            $model->attributes = $params;
            $model->gudang_id = $params['form']['gudang']['id'];
            $model->cabang_id = $params['form']['tujuan']['id'];
            $model->pengirim_id = $_SESSION['user']['id'];
        }

        if ($model->save()) {
            if (isset($params['status'])) {
                if ((isset($params['detail']) and ! empty($params['detail'])) and $model->status == 'accept') {
                    foreach ($params['detail'] as $val) {
                        //update stok gudang
                        $keterangan = 'transfer';
                        $stok = new \app\models\KartuStok();
                        $update = $stok->process('out', $model->tgl_terima, $model->nota, $val['produk']['id'], $val['jumlah'], $model->gudang_id, $val['harga'], $keterangan, $model->id);

                        //update stok cabang
                        $keterangan = 'transfer';
                        $stok = new \app\models\KartuStok();
                        $update = $stok->process('in', $model->tgl_terima, $model->nota, $val['produk']['id'], $val['jumlah'], $model->cabang_id, $val['harga'], $keterangan, $model->id);
                    }
                }
            } else {
                $delDetail = \app\models\TransferDet::deleteAll('transfer_id=' . $id);
                if (isset($params['detail']) and ! empty($params['detail'])) {
                    foreach ($params['detail'] as $val) {
                        $det = new \app\models\TransferDet();
                        $det->id_barang = $val['produk']['id'];
                        $det->transfer_id = $model->id;
                        $det->qty = $val['jumlah'];
                        $det->harga = $val['harga'];
                        $det->save();
                    }
                }
            }

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $delDetail = \app\models\TransferDet::deleteAll('transfer_id=' . $id);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Transfer::findOne($id)) !== null) {
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

}

?>
