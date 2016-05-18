<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                    'logout' => ['get'],
                    'session' => ['get'],
                    'coba' => ['get'],
                    'penjualan' => ['post'],
                ],
            ]
        ];
    }

    public function actionPenjualan() {
        $params = json_decode(file_get_contents("php://input"), true);

        if (isset($params['bulan']) and ! empty($params['bulan'])) {
            $month = $params['bulan'];
            $year = (isset($params['tahun']) && !empty($params['tahun'])) ? $params['tahun'] : date("Y");
        } else {
            $month = date("m");
            $year = date("Y");
        }
        session_start();
        $pen = \app\models\Penjualan::find()
                ->joinWith('cabang')
                ->where('month(tanggal) = "' . $month . '" and year(tanggal) = "' . $year . '"')
                ->andWhere(['IN', 'cabang_id', $_SESSION['user']['cabang_id']])
                ->all();

        $dt = array();
        foreach ($pen as $val) {
            $dt[$val->cabang_id] = $val->cabang->nama;
            $dt[$val->tanggal][$val->cabang_id]['total'] = (isset($dt[$val->tanggal][$val->cabang_id]) ? $dt[$val->tanggal][$val->cabang_id]['total'] : 0 ) + $val->total;
        }

        $bts = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $awal = $year . '-' . $month . '-01';
        $akhir = $year . '-' . $month . '-' . $bts;

        $start = new \DateTime($awal);
        $end = new \DateTime($akhir);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);

        $data = array();
        foreach ($daterange as $date) {

            $dy = $date->format('d');
            $mn = $date->format('m');
            $yr = $date->format('Y');

            $tgl = mktime(0, 0, 0, $mn, $dy, $yr);
            $tanggal = date("Y-m-d", $tgl);

            if (isset($dt[$tanggal])) {
                foreach ($_SESSION['user']['cabang'] as $val) {
                    if (isset($dt[$tanggal][$val['id']])) {
                        $data[$val['id']]['data'][] = $dt[$tanggal][$val['id']]['total'];
                        $data[$val['id']]['name'] = $val['nama'];
                    } else {
                        $data[$val['id']]['data'][] = 0;
                        $data[$val['id']]['name'] = $val['nama'];
                    }
                }
            } else {
                foreach ($_SESSION['user']['cabang'] as $val) {
                    $data[$val['id']]['data'][] = 0;
                    $data[$val['id']]['name'] = $val['nama'];
                }
            }

            $title = 'Penjualan Bulan ' . date('m', $tgl) . ' Tahun ' . date("Y", $tgl);
            $kategori[] = date("d M y", $tgl);
        }
        echo json_encode(array('data' => $data, 'kategori' => $kategori, 'title' => $title));
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

    public function actionCoba() {
        $tes = new \app\models\KartuStok();
        $saldoAwal = $tes->saldo('balance', '2015-05-30', array('produk_id' => 255, 'cabang' => 1));
//        echo json_encode($saldoAwal);
        echo date("Y-m-d", strtotime(1442408334));
    }

    public function actionSession() {
        session_start();
        echo json_encode(array('status' => 1, 'data' => array_filter($_SESSION)), JSON_PRETTY_PRINT);
    }

    public function actionLogout() {
        session_start();
        session_destroy();
    }

    public function actionLogin() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = User::find()->where(['username' => $params['username'], 'password' => sha1($params['password'])])->one();

        if (!empty($model)) {
            session_start();
            $_SESSION['user']['id'] = $model->id;
            $_SESSION['user']['username'] = $model->username;
            $_SESSION['user']['nama'] = $model->nama;
            $akses = (isset($model->roles->akses)) ? $model->roles->akses : '[]';
            $_SESSION['user']['akses'] = json_decode($akses);
            $_SESSION['user']['settings'] = json_decode($model->settings);

            //mencari hak akses cabang
            $query = new Query;
            $query->from('m_cabang')
                    ->join('JOIN', 'm_akses_cabang', 'm_akses_cabang.cabang_id=m_cabang.id')
                    ->select("m_cabang.*")
                    ->where("m_akses_cabang.roles_id = " . $model->roles_id);

            $command = $query->createCommand();
            $cabang = $command->queryAll();
            $_SESSION['user']['cabang'] = $cabang;

            $cbg = array();
            foreach ($cabang as $val) {
                $cbg[] = $val['id'];
            }
            $_SESSION['user']['cabang_id'] = $cbg;

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($_SESSION)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => "Authentication Systems gagal, Username atau password Anda salah."), JSON_PRETTY_PRINT);
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
