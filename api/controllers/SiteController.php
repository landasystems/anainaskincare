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

    public function actionCoba() {
        $produk = \app\models\Barang::find()->all();
        foreach($produk as $val){
            $cabang = \app\models\Cabang::find()->all();
            foreach($cabang as $vCabang){
                $new = new \app\models\Harga();
                $new->cabang_id = $vCabang->id;
                $new->produk_id = $val->id;
                $new->harga_jual = $val->harga_jual;
                $new->harga_beli = $val->harga_beli_terakhir;
                $new->save();
            }
        }
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
