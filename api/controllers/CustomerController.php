<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class CustomerController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'cari' => ['get'],
                    'upload' => ['post'],
                    'removegambar' => ['post'],
                ],
            ]
        ];
    }

    public function actionUpload() {
        if (!empty($_FILES)) {

            $tempPath = $_FILES['file']['tmp_name'];
            $newName = \Yii::$app->landa->urlParsing($_FILES['file']['name']);

            $uploadPath = \Yii::$app->params['pathImg'] . $_GET['folder'] . DIRECTORY_SEPARATOR . $newName;

            move_uploaded_file($tempPath, $uploadPath);
            $a = \Yii::$app->landa->createImg($_GET['folder'] . '/', $newName, $_POST['kode']);

            $answer = array('answer' => 'File transfer completed', 'name' => $newName);
            if ($answer['answer'] == "File transfer completed") {
                $customer = Customer::find()->where('kode = "' . $_POST['kode'] . '"')->one();
                $foto = json_decode($customer->foto, true);
                $foto[] = array('name' => $newName);
                $customer->foto = json_encode($foto);
                $customer->save();
            }

            echo json_encode($answer);
        } else {
            echo 'No files';
        }
    }

    public function actionRemovegambar() {
        $params = json_decode(file_get_contents("php://input"), true);
        $customer = Customer::find()->where('kode = "' . $params['kode'] . '"')->one();
        $foto = json_decode($customer->foto, true);
        foreach ($foto as $key => $val) {
            if ($val['name'] == $params['nama']) {
                unset($foto[$key]);
                \Yii::$app->landa->deleteImg('barang/', $params['kode'], $params['nama']);
            }
        }
        $customer->foto = json_encode($foto);
        $customer->save();

        echo json_encode($foto);
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

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('m_customer')
                ->select("*")
                ->where('is_deleted = "0" and (nama LIKE "%'.$params['nama'].'%" OR kode LIKE "%'.$params['nama'].'%")');

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "CAST(m_customer.kode AS UNSIGNED) ASC";
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
//                ->select('m_user.id as id', 'm_roles.nama as roles')
                ->from('m_customer')
//                ->where('')
                ->orderBy($sort)
                ->select("*");

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

        $data = array();
        foreach ($models as $key => $val) {
            $data[$key] = $val;
            $data[$key]['foto'] = json_decode($val['foto'], true);
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Customer();
        $model->attributes = $params;
        $model->tanggal_lahir = date('Y-m-d', strtotime($params['tanggal_lahir']));

        if ($model->save()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->attributes = $params;
        $model->tanggal_lahir = date('Y-m-d', strtotime($params['tanggal_lahir']));

        if ($model->save()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
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
        if (($model = Customer::findOne($id)) !== null) {
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
