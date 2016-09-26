<?php

namespace app\controllers;

use Yii;
use app\models\Penjualan;
use app\models\PenjualanDet;
use app\models\Pinjaman;
use app\models\Customer;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class PenjualanController extends Controller {

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
                    'nm_customer' => ['get'],
                    'det_produk' => ['get'],
                    'kode_cabang' => ['get'],
                    'cari' => ['get'],
                    'dokter' => ['post'],
                    'terapis' => ['post'],
                    'saveprint' => ['post'],
                    'getpaket' => ['post'],
                    'getdiskon' => ['get'],
                ],
            ]
        ];
    }

    public function actionGetdiskon() {
        $query = new query;
        $query->select("*")
                ->from("m_produk")
                ->where("id = 1494");
        $command = $query->createCommand();
        $model = $command->query()->read();

        if (empty($model)) {
            echo json_encode(array("s" => 0));
        } else {
            $model['produk'] = array('id' => $model['id'], 'nama' => $model['nama']);
            $model['harga'] = (int) 0;
            $model['jumlah'] = (int) 1;
            $model['diskon'] = (int) 0;
            $model['diskonpersen'] = (int) 0;
            echo json_encode(array("s" => 1, "diskon" => $model));
        }
    }

    public function actionGetpaket() {
        $listPaket = array();
        $params = json_decode(file_get_contents("php://input"), true);
        $id = isset($params['paket_id']) ? $params['paket_id'] : '';
        $penjualan = isset($params['penjualan_id']) ? $params['penjualan_id'] : '';

        $query = new Query;
        $query->from('penjualan_det as pd')
                ->join('Left JOIN', 'm_produk as mp', 'pd.produk_id = mp.id')
                ->select('mp.nama, mp.id as barang_id, pd.*')
                ->where('pd.paket_id = ' . $id)
                ->orderBy('pd.id ASC')
                ->andWhere('pd.penjualan_id = ' . $penjualan);
        $command = $query->createCommand();
        $models = $command->queryAll();

        $total = 0;
        $jml = 1;
        foreach ($models as $key => $val) {
            if (empty($val['harga'])) {
                $listPaket[$key] = $val;
                $listPaket[$key]['produk'] = array('id' => $val['barang_id'], 'nama' => $val['nama']);
                $listPaket[$key]['jml'] = $val['jumlah'] / $jml;
            } else {
                $jml = $val['jumlah'];
            }
        }

        echo json_encode(array('status' => 1, 'data' => $listPaket, 'total' => $total), JSON_PRETTY_PRINT);
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

    public function actionSaveprint() {
        $params = json_decode(file_get_contents("php://input"), true);

        $penjualan = Penjualan::findOne($params['id']);
        if (!empty($penjualan)) {
            $penjualan->print += 1;
            $penjualan->save();
        }
    }

    public function actionIndex() {
        session_start();

        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "penjualan.id DESC";
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
                ->orderBy($sort)
                ->limit($limit)
                ->from('penjualan')
                ->join('LEFT JOIN', 'm_cabang', 'penjualan.cabang_id = m_cabang.id')
                ->join('LEFT JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('LEFT JOIN', 'm_user', 'm_user.id = penjualan.created_by')
                ->select('m_user.nama as petugas, penjualan.created_at, m_cabang.nama as cabang, m_customer.nama as customer, penjualan.print, penjualan.kode as kode, penjualan.tanggal as tanggal,
                    penjualan.keterangan as keterangan, penjualan.total as total, penjualan.cash as cash, penjualan.credit as credit, penjualan.status as status,
                    penjualan.kode as kode, penjualan.id as id,  penjualan.customer_id as customer_id, penjualan.cabang_id as cabang_id,penjualan.atm,
                    m_customer.no_tlp as no_tlp, m_customer.email as email, m_customer.alamat as alamat')
                ->where(['penjualan.cabang_id' => $_SESSION['user']['cabang_id']]);

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key != 'm_produk.type') {
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
        }
        $_SESSION['query'] = $filter;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);

        $data = $model->attributes;
        $cus = \app\models\Customer::find()
                ->where(['id' => $model['customer_id']])
                ->One();
        $idcus = (isset($cus->id)) ? $cus->id : '';
        $cus_nama = (isset($cus->nama)) ? $cus->nama : '';
        $no_tlp = (isset($cus->no_tlp)) ? $cus->no_tlp : '';
        $email = (isset($cus->email)) ? $cus->email : '';
        $alamat = (isset($cus->alamat)) ? $cus->alamat : '';

        $cab = \app\models\Cabang::find()
                ->where(['id' => $model['cabang_id']])
                ->One();

        $idcab = (isset($cab->id)) ? $cab->id : '';
        $cabkode = (isset($cab->kode)) ? $cab->kode : '';
        $cabnama = (isset($cab->nama)) ? $cab->nama : '';
        $cabalamat = (isset($cab->alamat)) ? $cab->alamat : '';
        $cabnotlp = (isset($cab->no_tlp)) ? $cab->no_tlp : '';
        $cabemail = (isset($cab->email)) ? $cab->email : '';
        $cabis_deleted = (isset($cab->is_deleted)) ? $cab->is_deleted : '';

        $user = \app\models\Pengguna::find()
                ->where(['id' => $model['created_by']])
                ->One();

        $data['user'] = [
            'id' => isset($user->id) ? $user->id : '-',
            'nama' => isset($user->nama) ? $user->nama : '-',
        ];

        $data['customers'] = [
            'id' => $idcus,
            'nama' => $cus_nama,
            'no_tlp' => $no_tlp,
            'email' => $email,
            'alamat' => $alamat,
            'kode' => (isset($cus->kode)) ? $cus->kode : '',
        ];

        $data['cabang'] = [
            'id' => $idcab,
            'kode' => $cabkode,
            'nama' => $cabnama,
            'alamat' => $cabalamat,
            'no_tlp' => $cabnotlp,
            'email' => $cabemail,
            'is_deleted' => $cabis_deleted,
        ];

        // terapis
        $query2 = new Query;
        $query2->from('m_pegawai')
                ->select('*')
                ->where('is_deleted = "0" and jabatan = "terapis" and cabang_id="' . $model['cabang_id'] . '"');

        $command2 = $query2->createCommand();
        $listterapis = $command2->queryAll();

        // dokter
        $query3 = new Query;
        $query3->from('m_pegawai')
                ->select('*')
                ->where('is_deleted = "0" and jabatan = "dokter" and cabang_id="' . $model['cabang_id'] . '"');

        $command3 = $query3->createCommand();
        $listdokter = $command3->queryAll();

        $det = PenjualanDet::find()
                ->with(['barang'])
                ->orderBy('id')
                ->where(['penjualan_id' => $model['id']])
                ->all();

        $detail = array();

        foreach ($det as $key => $val) {
//            if ($val['harga'] >= 0 or ( $val['harga'] >= 0 and ! empty($val['paket_id']))) {
            $detail[$key] = $val->attributes;
            $namaBarang = (isset($val->barang->nama)) ? $val->barang->nama : '';
            $hargaBarang = (isset($val->barang->harga_beli_terakhir)) ? $val->barang->harga_beli_terakhir : '';
            $jualBarang = (isset($val->barang->harga_jual)) ? $val->barang->harga_jual : '';
            $dokter = (isset($val->dokter->nama)) ? $val->dokter->nama : '';
            $terapis = (isset($val->terapis->nama)) ? $val->terapis->nama : '';
            $detail[$key]['produk'] = ['id' => $val->produk_id, 'nama' => $namaBarang, 'harga_beli_terakhir' => $hargaBarang, 'harga_jual' => $jualBarang];
            $detail[$key]['terapis'] = ['id' => $val->pegawai_terapis_id, 'nama' => $terapis];
            $detail[$key]['dokter'] = ['id' => $val->pegawai_dokter_id, 'nama' => $dokter];
            $detail[$key]['diskonpersen'] = ($val->harga == 0) ? 0 : ($val->diskon / $val->harga) * 100;
//            }
        }

//        $query = new Query;
//        $query->from('penjualan_det as pd')
//                ->join('Left JOIN', 'm_produk as mp', 'pd.produk_id = mp.id')
//                ->select('mp.nama, mp.id as barang_id, pd.*')
//                ->orderBy('pd.id ASC')
//                ->andWhere('pd.penjualan_id = ' . $id);
//        $command = $query->createCommand();
//        $models = $command->queryAll();
//
//        foreach ($models as $key => $val) {
//            $listPaket[$key] = $val;
//            if ($val['type'] == "Paket" and empty($val['harga'])) {
//                $nama = ' - ' . $val['nama'];
//            } else {
//                $nama = $val['nama'];
//            }
//            $listPaket[$key]['produk'] = array('id' => $val['barang_id'], 'nama' => $nama);
//        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'detail' => $detail, 'terapis' => $listterapis, 'dokter' => $listdokter), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Penjualan();
        $model->attributes = $params['penjualan'];
        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));
        $model->customer_id = isset($params['penjualan']['customers']['id']) ? $params['penjualan']['customers']['id'] : null;
        $model->cabang_id = $params['penjualan']['cabang']['id'];

        if (isset($params['penjualan']['customers']['id'])) {
            $cust = Customer::findOne($params['penjualan']['customers']['id']);
//            echo 'a';
        } else {
            $cust = new Customer;
//            echo 'b';
        }

        $cust->nama = isset($params['penjualan']['customers']['nama']) ? $params['penjualan']['customers']['nama'] : '';
        $cust->kode = isset($params['penjualan']['kode_cust']) ? $params['penjualan']['kode_cust'] : '';
        $cust->alamat = isset($params['penjualan']['alamat']) ? $params['penjualan']['alamat'] : '';
        $cust->no_tlp = isset($params['penjualan']['no_tlp']) ? $params['penjualan']['no_tlp'] : '';
        $cust->email = isset($params['penjualan']['email']) ? $params['penjualan']['email'] : '';

        if ($cust->validate()) {
            $cust->save();
            $model->customer_id = $cust->id;
        }

        if ($model->validate() && $cust->validate()) {
            if ($model->save()) {
                if ($model->status == "Pesan") {
                    if ($model->credit > 0) {
                        $pinjaman = new Pinjaman();
                        $pinjaman->penjualan_id = $model->id;
                        $pinjaman->debet = $model->credit;
                        $pinjaman->tanggal_transaksi = $model->tanggal;
                        $pinjaman->status = 'Belum Lunas';
                        $pinjaman->save();
                    }
                }

                foreach ($params['penjualandet'] as $data) {
                    $det = new PenjualanDet();
                    $det->attributes = $data;
                    $det->penjualan_id = $model->id;
                    $det->produk_id = $data['produk']['id'];
                    $det->pegawai_terapis_id = isset($data['terapis']['id']) ? $data['terapis']['id'] : '';
                    $det->pegawai_dokter_id = isset($data['dokter']['id']) ? $data['dokter']['id'] : '';

                    if ($det->type == "Paket") {
                        $det->paket_id = $data['produk']['id'];
                    }

                    if ($det->save()) {

                        if ($det->type == 'Paket') {
                            $sPaket = \app\models\PaketDet::find()->where('paket_id=' . $det->produk_id)->all();
                            foreach ($sPaket as $vPaket) {
                                $detPaket = new PenjualanDet();
                                $detPaket->penjualan_id = $model->id;
                                $detPaket->produk_id = $vPaket->barang_id;
                                $detPaket->jumlah = $vPaket->jml * $det->jumlah;
                                $detPaket->type = 'Paket';
                                $detPaket->harga = 0;
                                $detPaket->paket_id = $data['produk']['id'];
                                $detPaket->save();

                                if ($model->status == 'Selesai') {
                                    $keterangan = 'penjualan';
                                    $stok = new \app\models\KartuStok();
                                    $update = $stok->process('out', $model->tanggal, $model->kode, $detPaket->produk_id, $detPaket->jumlah, $model->cabang_id, $detPaket->harga, $keterangan, $model->id);
                                }
                            }
                        }

                        //======== AKTIFKAN JIKA HARGA PER CABANG BERBEDA ===========//
                        //======== SIMPAN HARGA JUAL BARU ============//
//                    $harga = \app\models\Harga::find()->where('cabang_id="' . $model->cabang_id . '" and produk_id="' . $det->produk_id . '"')->one();
//                    if (!empty($harga)) {
//                        $harga->harga_jual = $det->harga;
//                        $harga->save();
//                    } else {
//                        $harga = new \app\models\Harga();
//                        $harga->cabang_id = $model->cabang_id;
//                        $harga->produk_id = $modelDet->produk_id;
//                        $harga->harga_jual = $modelDet->harga;
//                        $harga->save();
//                    }

                        if ($model->status == 'Selesai') {
                            $keterangan = 'penjualan';
                            $stok = new \app\models\KartuStok();
                            $update = $stok->process('out', $model->tanggal, $model->kode, $det->produk_id, $det->jumlah, $model->cabang_id, $det->harga, $keterangan, $model->id);
                        }
                    }
                }

                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
            } else {
                $this->setHeader(400);
                echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => array_merge($cust->errors, $model->errors)), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $id_det = array();
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->attributes = $params['penjualan'];
        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));
        $model->customer_id = isset($params['penjualan']['customers']['id']) ? $params['penjualan']['customers']['id'] : null;
        $model->cabang_id = $params['penjualan']['cabang']['id'];

        if (isset($params['penjualan']['customers']['id'])) {
            $cust = Customer::findOne($params['penjualan']['customers']['id']);
        } else {
            $cust = new Customer;
        }

        $cust->nama = isset($params['penjualan']['customers']['nama']) ? $params['penjualan']['customers']['nama'] : '';
        $cust->kode = isset($params['penjualan']['kode_cust']) ? $params['penjualan']['kode_cust'] : '';
        $cust->alamat = isset($params['penjualan']['alamat']) ? $params['penjualan']['alamat'] : '';
        $cust->no_tlp = isset($params['penjualan']['no_tlp']) ? $params['penjualan']['no_tlp'] : '';
        $cust->email = isset($params['penjualan']['email']) ? $params['penjualan']['email'] : '';

        if ($cust->validate()) {
            $cust->save();
            $model->customer_id = $cust->id;
        }

        if ($model->validate() && $cust->validate()) {
            if ($model->save()) {
                if ($model->status == 'Selesai') {
                    if ($model->credit > 0) {
                        $pinjaman = Pinjaman::find()->where('penjualan_id=' . $model->id)->one();
                        if (empty($pinjaman)) {
                            $pinjaman = new Pinjaman();
                        }

                        $pinjaman->penjualan_id = $model->id;
                        $pinjaman->debet = $model->credit;
                        $pinjaman->status = 'Belum Lunas';
                        $pinjaman->save();
                        //stock
                    }
                }
                $penjualanDet = $params['penjualandet'];

                //hapus kartu stok
                $keterangan = 'penjualan';
                $stok = new \app\models\KartuStok();
                $hapus = $stok->hapusKartu($keterangan, $model->id);

                foreach ($params['penjualandet'] as $val) {
                    $det = PenjualanDet::findOne($val['id']);
                    if (empty($det)) {
                        $det = new PenjualanDet();
                    }
                    $det->attributes = $val;
                    $det->produk_id = $val['produk']['id'];
                    $det->pegawai_terapis_id = isset($val['terapis']['id']) ? $val['terapis']['id'] : null;
                    $det->pegawai_dokter_id = isset($val['dokter']['id']) ? $val['dokter']['id'] : null;
                    $det->penjualan_id = $model->id;
                    if ($det->save()) {
                        if ($det->type == 'Paket') {
                            $delDet = PenjualanDet::deleteAll('penjualan_id = ' . $model->id . ' and paket_id = ' . $det->produk_id . ' and harga > 0');
                            $sPaket = \app\models\PaketDet::find()->where('paket_id=' . $det->produk_id)->all();
                            foreach ($sPaket as $vPaket) {
                                $detPaket = new PenjualanDet();
                                $detPaket->penjualan_id = $model->id;
                                $detPaket->produk_id = $vPaket->barang_id;
                                $detPaket->jumlah = $vPaket->jml * $det->jumlah;
                                $detPaket->type = 'Paket';
                                $detPaket->harga = 0;
                                $detPaket->paket_id = $data['produk']['id'];
                                $detPaket->save();

                                if ($model->status == 'Selesai') {
                                    $keterangan = 'penjualan';
                                    $stok = new \app\models\KartuStok();
                                    $update = $stok->process('out', $model->tanggal, $model->kode, $detPaket->produk_id, $detPaket->jumlah, $model->cabang_id, $detPaket->harga, $keterangan, $model->id);
                                }
                            }
                        }

                        //======== AKTIFKAN JIKA HARGA PER CABANG BERBEDA ===========//
                        //======== SIMPAN HARGA JUAL BARU ============//
//                    $harga = \app\models\Harga::find()->where('cabang_id="' . $model->cabang_id . '" and produk_id="' . $det->produk_id . '"')->one();
//                    if (!empty($harga)) {
//                        $harga->harga_jual = $det->harga;
//                        $harga->save();
//                    } else {
//                        $harga = new \app\models\Harga();
//                        $harga->cabang_id = $model->cabang_id;
//                        $harga->produk_id = $modelDet->produk_id;
//                        $harga->harga_jual = $modelDet->harga;
//                        $harga->save();
//                    }

                        $id_det[] = $det->id;
                        if ($model->status == 'Selesai') {
                            $update = $stok->process('out', $model->tanggal, $model->kode, $det->produk_id, $det->jumlah, $model->cabang_id, $det->harga, $keterangan, $model->id);
                        }
                    }
                }
                $deleteDetail = PenjualanDet::deleteAll('id NOT IN (' . implode(',', $id_det) . ') AND penjualan_id=' . $model->id);

                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
            } else {
                $this->setHeader(400);
                echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => array_merge($cust->errors, $model->errors)), JSON_PRETTY_PRINT);
        }
    }

    public function actionCari() {
        session_start();
        $params = $_REQUEST;
        $query = new Query;
        $query->from('penjualan')
                ->select("penjualan.*")
                ->where(['penjualan.cabang_id' => $_SESSION['user']['cabang_id']])
                ->andWhere(['like', 'kode', $params['nama']])
                ->limit(10);
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
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

    public function actionProduk() {
        $query = new Query;
        $query->from('m_produk')
                ->select('*')
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'produk' => $models));
    }

    public function actionDokter() {
        session_start();
        $query = new Query;
        $query->from('m_pegawai')
                ->select('*')
                ->where("is_deleted = '0' and jabatan = 'dokter' and ");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'dokter' => $models));
    }

    public function actionTerapis() {
        session_start();
        $query = new Query;
        $query->from('m_pegawai')
                ->select('*')
                ->where("is_deleted = '0' and jabatan = 'terapis' ");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'terapis' => $models));
    }

    public function actionNm_customer($id) {
        $query = new Query;

        $query->from('m_customer')
                ->where('id="' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->query()->read();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionKode_cabang($id) {
        $query = new Query;

        $query->from('m_cabang')
                ->where('id="' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->query()->read();
        $code = $models['kode'];

        $query2 = new Query;
        $query2->from('penjualan')
                ->select('kode')
                ->where('cabang_id="' . $id . '"')
                ->orderBy('kode DESC')
                ->limit(1);

        $command2 = $query2->createCommand();
        $models2 = $command2->query()->read();
        $kode_mdl = (substr($models2['kode'], -5) + 1);
        $kode = substr('00000' . $kode_mdl, strlen($kode_mdl));

        // terapis
        $query2 = new Query;
        $query2->from('m_pegawai')
                ->select('*')
                ->where('is_deleted = "0" and jabatan = "terapis" and cabang_id="' . $id . '"');

        $command2 = $query2->createCommand();
        $terapis = $command2->queryAll();

        // dokter
        $query3 = new Query;
        $query3->from('m_pegawai')
                ->select('*')
                ->where('is_deleted = "0" and jabatan = "dokter" and cabang_id="' . $id . '"');

        $command3 = $query3->createCommand();
        $dokter = $command3->queryAll();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => 'JUAL/' . $code . '/' . $kode, 'terapis' => $terapis, 'dokter' => $dokter));
    }

    public function actionDet_produk($id) {
        $query = new Query;
        $query->from('m_produk')
                ->where('id="' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryOne();

        $this->setHeader(200);

        echo json_encode(array('produk' => $models));
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = PenjualanDet::deleteAll(['penjualan_id' => $id]);

        //hapus kartu stok
        $keterangan = 'penjualan';
        $stok = new \app\models\KartuStok();
        $hapus = $stok->hapusKartu($keterangan, $id);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Penjualan::findOne($id)) !== null) {
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
        $filter = $_SESSION['query'];
        return $this->render("excel", ['filter' => $filter]);
    }

}

?>
