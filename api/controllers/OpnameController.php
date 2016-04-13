<?php

namespace app\controllers;

use Yii;
use app\models\Cabang;
use app\models\Barang;
use app\models\StokOpname;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class OpnameController extends Controller {

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
                    'getproduk' => ['post'],
                    'kode_cabang' => ['get']
                ],
            ]
        ];
    }

    public function actionGetproduk() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "mp.kode ASC";
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
        $query->from('m_produk as mp')
                ->join('Left Join', 'm_satuan as ms', 'mp.satuan_id = ms.id')
                ->join('Left Join', 'm_kategori as mk', 'mk.id = mp.kategori_id')
                ->select('mp.*, ms.nama as satuan, mk.nama as kategori');
        $query->where('mp.type = "Barang"');

        //filter
        if (isset($params['kategori_id']) and ! empty($params['kategori_id'])) {
            $query->andWhere(['mp.kategori_id' => $params['kategori_id']['id']]);
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        $data = array();
        foreach ($models as $key => $val) {
            $stok = 0;

            $cabang = isset($params['cabang_id']) ? $params['cabang_id'] : $_SESSION['user']['cabang'][0]['id'];

            $st = new Barang;
            $stok = $st->stok($val['id'], $cabang);

            //============== AKTIFKAN JIKA HARGA PER CABANG BERBEDA ==========//
//            $harga = \app\models\Harga::find()->where('cabang_id = "' . $cabang . '" and produk_id="' . $val['id'] . '" ')->one();
//            $hargaJual = isset($harga['harga_jual']) ? $harga['harga_jual'] : $val['harga_jual'];

            $data[$key] = $val;
//            $data[$key]['harga_jual'] = $hargaJual; AKTIFKAN JIKA HARGA PER CABANG BERBEDA
            $data[$key]['harga_jual'] = $val['harga_jual'];
            $data[$key]['stok'] = $stok;
            $data[$key]['real_stok'] = $stok;
        }

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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

    public function actionKode_cabang($id) {
        $query = new Query;

        $query->from('m_cabang')
                ->where('id = "' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->query()->read();
        $code = $models['kode'];

        $query2 = new Query;
        $query2->from('stok_opname')
                ->select('kode')
                ->where('cabang_id="' . $id . '"')
                ->orderBy('id DESC')
                ->limit(1);

        $command2 = $query2->createCommand();
        $models2 = $command2->query()->read();

        $kode_mdl = (substr($models2['kode'], -5) + 1);
        $kode = substr('00000' . $kode_mdl, strlen($kode_mdl));

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => 'SO/' . $code . '/' . $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "so.id DESC";
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
                ->from('stok_opname as so')
                ->join('LEFT JOIN', 'm_cabang as mc', 'mc.id = so.cabang_id')
                ->join('LEFT JOIN', 'm_kategori as mk', 'mk.id = so.kategori_id')
                ->join('LEFT JOIN', 'm_user as mu', 'mu.id = so.created_by')
                ->orderBy($sort)
                ->select("so.*, mc.nama as cabang, mk.nama as kategori, mu.nama as petugas");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {
        $model = $this->findModel($id);

        $query = new Query;
        $query->from('stok_opname as so')
                ->join('LEFT JOIN', 'm_cabang as mc', 'mc.id = so.cabang_id')
                ->join('LEFT JOIN', 'm_kategori as mk', 'mk.id = so.kategori_id')
                ->join('LEFT JOIN', 'm_user as mu', 'mu.id = so.created_by')
                ->where('so.id = "' . $id . '"')
                ->select("so.*, mc.nama as cabang, mk.id as kategori_id, mk.nama as kategori, mu.nama as petugas");

        $command = $query->createCommand();
        $models = $command->query()->read();

        $data['data']['id'] = $models['id'];
        $data['data']['kode'] = $models['kode'];
        $data['data']['tanggal'] = $models['tanggal'];
        $data['data']['cabang_id'] = $models['cabang_id'];
        $data['data']['kategori_id'] = array('id' => $models['kategori_id'], 'nama' => $models['kategori']);

        $det = json_decode($models['stok'], true);
        $detail = array();
        $idDet = array();
        foreach ($det as $key => $val) {
            $detail[$key] = $val;
            $idDet[] = $key;
        }

        $query = new Query;
        $query->from('m_produk as mp')
                ->join('Left Join', 'm_satuan as ms', 'mp.satuan_id = ms.id')
                ->join('Left Join', 'm_kategori as mk', 'mk.id = mp.kategori_id')
                ->select('mp.*, ms.nama as satuan, mk.nama as kategori');
        $query->where('mp.type = "Barang"');

        if ($models['is_tmp'] == 1) {
            $query->andWhere('mp.kategori_id=' . $models['kategori_id']);
        } else if ($models['is_tmp'] == 0) {
            $query->andWhere(['mp.id' => $idDet]);
        }

        $command = $query->createCommand();
        $md = $command->queryAll();

        foreach ($md as $key2 => $vl) {
            $data['detail'][$key2] = $vl;
            $data['detail'][$key2]['stok'] = isset($detail[$vl['id']]['stok_app']) ? $detail[$vl['id']]['stok_app'] : 0;
            $data['detail'][$key2]['real_stok'] = isset($detail[$vl['id']]['stok_real']) ? $detail[$vl['id']]['stok_real'] : 0;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => isset($data['data']) ? $data['data'] : array(), 'detail' => isset($data['detail']) ? $data['detail'] : array()), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $kategori = (isset($params['form']['kategori_id']['id']) && !empty($params['form']['kategori_id']['id'])) ? $params['form']['kategori_id']['id'] : 0;

        $cek = StokOpname::find()->where(['kategori_id' => $kategori])
                ->andWhere(['cabang_id' => $params['form']['cabang_id']])
                ->andWhere(['month(tanggal)' => date("m", strtotime($params['form']['tanggal']))])
                ->andWhere(['year(tanggal)' => date("Y", strtotime($params['form']['tanggal']))])
                ->andWhere(['is_tmp' => 0])
                ->all();

        if (isset($params['form']['id'])) {
            $model = $this->findModel($params['form']['id']);
        } else {
            $model = new StokOpname();
        }

        $model->attributes = $params['form'];
        $model->kategori_id = $kategori;

        $stok = array();
        $masuk = array();
        $keluar = array();
        if (isset($params['detailopname']) && !empty($params['detailopname'])) {
            foreach ($params['detailopname'] as $key => $vl) {
                $stok[$vl['id']]['produk_id'] = $vl['id'];
                $stok[$vl['id']]['stok_app'] = $vl['stok'];
                $stok[$vl['id']]['stok_real'] = $vl['real_stok'];
            }
        }

        $model->stok = json_encode($stok);

        if ($model->validate() && empty($cek)) {
            if ($model->save()) {
                if ($model->is_tmp == 0) {
                    if (isset($params['detailopname']) && !empty($params['detailopname'])) {
                        foreach ($params['detailopname'] as $key => $vl) {
                            if ($model->is_tmp == 0) {
                                $selisih = $vl['stok'] - $vl['real_stok'];

                                if ($selisih < 0) {
                                    //update stok cabang
                                    $keterangan = 'opname';
                                    $kartuStok = new \app\models\KartuStok();
                                    $update = $kartuStok->process('in', $model->tanggal, $model->kode, $vl['id'], ($selisih * -1), $model->cabang_id, 0, $keterangan, $model->id);
                                } else if ($selisih > 0) {
                                    //update stok cabang
                                    $keterangan = 'opname';
                                    $kartuStok = new \app\models\KartuStok();
                                    $update = $kartuStok->process('out', $model->tanggal, $model->kode, $vl['id'], $selisih, $model->cabang_id, 0, $keterangan, $model->id);
                                }
                            }
                        }
                    }
                }

                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array()), JSON_PRETTY_PRINT);
            } else {
                $this->setHeader(400);
                echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
            }
        } else if (!empty($cek)) {
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => 'Stok opname telah tercatat pada sistem'), JSON_PRETTY_PRINT);
        } else {
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = StokOpname::findOne($id)) !== null) {
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
