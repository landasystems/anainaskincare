<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class LandaCore extends Component {

    private $normalizeChars = array(
        'ï¿½' => 'S', 'ï¿½' => 's', 'ï¿½' => 'Dj', 'ï¿½' => 'Z', 'ï¿½' => 'z', 'ï¿½' => 'A', 'ï¿½' => 'A', 'ï¿½' => 'A', 'ï¿½' => 'A', 'ï¿½' => 'A',
        'ï¿½' => 'A', 'ï¿½' => 'A', 'ï¿½' => 'C', 'ï¿½' => 'E', 'ï¿½' => 'E', 'ï¿½' => 'E', 'ï¿½' => 'E', 'ï¿½' => 'I', 'ï¿½' => 'I', 'ï¿½' => 'I',
        'ï¿½' => 'I', 'ï¿½' => 'N', 'ï¿½' => 'O', 'ï¿½' => 'O', 'ï¿½' => 'O', 'ï¿½' => 'O', 'ï¿½' => 'O', 'ï¿½' => 'O', 'ï¿½' => 'U', 'ï¿½' => 'U',
        'ï¿½' => 'U', 'ï¿½' => 'U', 'ï¿½' => 'Y', 'ï¿½' => 'B', 'ï¿½' => 'Ss', 'ï¿½' => 'a', 'ï¿½' => 'a', 'ï¿½' => 'a', 'ï¿½' => 'a', 'ï¿½' => 'a',
        'ï¿½' => 'a', 'ï¿½' => 'a', 'ï¿½' => 'c', 'ï¿½' => 'e', 'ï¿½' => 'e', 'ï¿½' => 'e', 'ï¿½' => 'e', 'ï¿½' => 'i', 'ï¿½' => 'i', 'ï¿½' => 'i',
        'ï¿½' => 'i', 'ï¿½' => 'o', 'ï¿½' => 'n', 'ï¿½' => 'o', 'ï¿½' => 'o', 'ï¿½' => 'o', 'ï¿½' => 'o', 'ï¿½' => 'o', 'ï¿½' => 'o', 'ï¿½' => 'u',
        'ï¿½' => 'u', 'ï¿½' => 'u', 'ï¿½' => 'u', 'ï¿½' => 'y', 'ï¿½' => 'y', 'ï¿½' => 'b', 'ï¿½' => 'y', 'ï¿½' => 'f'
    );

    public function array_orderby() {
        $args = func_get_args();
        $data = array_shift($args);
        if (!is_array($data)) {
            return array();
        }
        $multisort_params = array();
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $row) {
                    $tmp[] = $row[$field];
                }
                $args[$n] = $tmp;
            }
            $multisort_params[] = &$args[$n];
        }
        $multisort_params[] = &$data;
        call_user_func_array('array_multisort', $multisort_params);
        return end($multisort_params);
    }

    public function urlParsing($string) {
        $arrDash = array("--", "---", "----", "-----");
        $string = strtolower(trim($string));
        $string = strtr($string, $this->normalizeChars);
        $string = preg_replace('/[^a-zA-Z0-9 -.]/', '', $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace(array("'", "\"", "&quot;"), "", $string);
        $string = str_replace($arrDash, "-", $string);
        return str_replace($arrDash, "-", $string);
    }

    public function urlImg($path, $filename = '', $id) {
        $filename = $this->urlParsing($filename);
        if (empty($filename) || empty($id)) {
            return array('small' => bu('img/150x150-noimage.jpg'), 'medium' => bu('img/350x350-noimage.jpg'), 'big' => bu('img/700x700-noimage.jpg'));
        } else {
            return array('small' => bu('images/' . $path . $id . '-150x150-' . $filename), 'medium' => bu('images/' . $path . $id . '-350x350-' . $filename), 'big' => bu('images/' . $path . $id . '-700x700-' . $filename));
        }
    }

    public function createImg($path, $filename, $id, $proporsional = false) {
        $newFileName = $this->urlParsing($filename);
        $small = \Yii::$app->params['pathImg'] . $path . $id . '-150x150-' . $newFileName;
        $medium = \Yii::$app->params['pathImg'] . $path . $id . '-350x350-' . $newFileName;
        $big = \Yii::$app->params['pathImg'] . $path . $id . '-700x700-' . $newFileName;
        //delete file, if any
        if (file_exists($small))
            unlink($small);
        if (file_exists($medium))
            unlink($medium);
        if (file_exists($big))
            unlink($big);

        $file = \Yii::$app->params['pathImg'] . $path . $filename;

        $this->smart_resize_image($file, $small, 150, 150, $proporsional, 80);
        $this->smart_resize_image($file, $medium, 350, 350, $proporsional, 80);
        $this->smart_resize_image($file, $big, 700, 700, $proporsional, 80);

        unlink(\Yii::$app->params['pathImg'] . $path . $filename);
    }

    public function deleteImg($path, $id, $fileName) {
        $small = \Yii::$app->params['pathImg'] . $path . $id . '-150x150-' . $fileName;
        $medium = \Yii::$app->params['pathImg'] . $path . $id . '-350x350-' . $fileName;
        $big = \Yii::$app->params['pathImg'] . $path . $id . '-700x700-' . $fileName;
        //delete file, if any
        if (file_exists($small))
            unlink($small);
        if (file_exists($medium))
            unlink($medium);
        if (file_exists($big))
            unlink($big);
    }

    public function registerAssetCss($file, $media = '') {
        $assetUrl = app()->assetManager->publish(Yii::getPathOfAlias('common.extensions.landa.assets'));
        cs()->registerCssFile($assetUrl . '/css/' . $file, $media);
    }

    public function registerAssetScript($file, $position = CClientScript::POS_BEGIN) {
        $assetUrl = app()->assetManager->publish(Yii::getPathOfAlias('common.extensions.landa.assets'));
        cs()->registerScriptFile($assetUrl . '/js/' . $file, $position);
    }

    public function ago($timestamp) {
        $difference = time() - strtotime($timestamp);
        $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'years', 'decade');
        $lengths = array('60', '60', '24', '7', '4.35', '12', '10');

        for ($j = 0; $difference >= $lengths[$j]; $j++)
            $difference /= $lengths[$j];

        $difference = round($difference);
        if ($difference != 1)
            $periods[$j] .= "s";

        return "$difference $periods[$j] ago";
    }

    public function option($data, $default = false) {
        $results = '';

        if ($default)
            $results .= CHtml::tag('option', array('value' => ''), t('choose', 'global'), true);

        foreach ($data as $value => $name) {
            $results .= CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
        }
        return $results;
    }

    public function rp($price = 0, $prefix = true, $decimal = 0) {
        if (isset($_GET['xls'])) //jika export excel ada, landa rp tidak berlaku
            return $price;

        if ($price === '-') {
            return '';
        } else {
            if ($prefix === "-") {
                return $price;
            } else {
                $rp = ($prefix) ? 'Rp. ' : '';

                if ($price < 0) {
                    $price = (float) $price * -1;
                    $result = '(' . $rp . number_format($price, $decimal, ",", ".") . ')';
                } else {
                    $price = (float) $price;
                    $result = $rp . number_format($price, $decimal, ",", ".");
                }
                return $result;
            }
        }
    }

    public function price($price = 0, $prefix = true, $decimal = 0) {
        if (isset($_GET['xls'])) //jika export excel ada, landa rp tidak berlaku
            return $price;

        if ($price === '-') {
            return '';
        } else {
            if ($prefix === "-") {
                return $price;
            } else {
                $rp = ($prefix) ? '' : '';

                if ($price < 0) {
                    $price = (float) $price * -1;
                    $result = '(' . $rp . number_format($price, $decimal, ",", ".") . ')';
                } else {
                    $price = (float) $price;
                    $result = $rp . number_format($price, $decimal, ",", ".");
                }
                return $result;
            }
        }
    }

    public function hp($phone = '') {
        if (strlen($phone) < 9) {
            $prefix = "";
        } else {
            $phone = str_replace('+62', '0', $phone);
            $prefix = "+62";

            $split = str_split($phone, 4);
            $phone = '';
            foreach ($split as $value) {
                $phone .= $value . '-';
            }
            $phone = " " . substr(substr($phone, 1), 0, strlen($phone) - 2);
        }
        return $prefix . $phone;
    }

    function deleteDir($dir) {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file)
                continue;
            if (is_dir("$dir/$file"))
                $this->deleteDir("$dir/$file");
            else
                unlink("$dir/$file");
        }
        rmdir($dir);
    }

    function selfAccess($userid) {
        if ($userid != user()->id)
            throw new CHttpException(403, 'You are not authorized to perform this action.');
    }

    public function yearly($start = 2010, $end = 0) {
        $results = '';
        for ($i = date('Y') + $end; $i >= $start; $i--) {
            $results[$i] = $i;
        }

        return $results;
    }

    public function monthly() {
        return array('1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember');
    }

    public function daysInMonth($month = '', $year = '') {
        if (empty($month))
            $month = date("m");
        if (empty($year))
            $year = date("Y");
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    public function checkAccess($auth_id, $action, $roles_id = "") {
        $this->loginRequired();
        if (empty($roles_id)) {
            if (isset(user()->isSuperUser) && user()->isSuperUser) {
                return true;
            } else {
                $roles = user()->roles;
            }
        } else { //jika ada roles id, untuk pengecekan user yang lain , selain login user
            if ($roles_id == -1) {
                return true;
            } else {
                $roles = RolesAuth::model()->findAll(array('condition' => 'roles_id=' . $roles_id, 'select' => 'id,crud,auth_id', 'index' => 'auth_id'));
            }
        }

        if (isset($roles[$auth_id])) {
            $arrCrud = json_decode($roles[$auth_id]["crud"], true);
            if (isset($arrCrud[$action]) && $arrCrud[$action] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function shuffle_assoc($list) {
        if (!is_array($list))
            return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }

    public function client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function client_browser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    public function inWords($number) {
        $max_size = pow(10, 18);
        if (!$number)
            return "zero";
        if (is_int($number) && $number < abs($max_size)) {
            switch ($number) {
                // set up some rules for converting digits to words
                case $number < 0:
                    $prefix = "negative";
                    $suffix = translateToWords(-1 * $number);
                    $string = $prefix . " " . $suffix;
                    break;
                case 1:
                    $string = "one";
                    break;
                case 2:
                    $string = "two";
                    break;
                case 3:
                    $string = "three";
                    break;
                case 4:
                    $string = "four";
                    break;
                case 5:
                    $string = "five";
                    break;
                case 6:
                    $string = "six";
                    break;
                case 7:
                    $string = "seven";
                    break;
                case 8:
                    $string = "eight";
                    break;
                case 9:
                    $string = "nine";
                    break;
                case 10:
                    $string = "ten";
                    break;
                case 11:
                    $string = "eleven";
                    break;
                case 12:
                    $string = "twelve";
                    break;
                case 13:
                    $string = "thirteen";
                    break;
                // fourteen handled later
                case 15:
                    $string = "fifteen";
                    break;
                case $number < 20:
                    $string = translateToWords($number % 10);
                    // eighteen only has one "t"
                    if ($number == 18) {
                        $suffix = "een";
                    } else {
                        $suffix = "teen";
                    }
                    $string .= $suffix;
                    break;
                case 20:
                    $string = "twenty";
                    break;
                case 30:
                    $string = "thirty";
                    break;
                case 40:
                    $string = "forty";
                    break;
                case 50:
                    $string = "fifty";
                    break;
                case 60:
                    $string = "sixty";
                    break;
                case 70:
                    $string = "seventy";
                    break;
                case 80:
                    $string = "eighty";
                    break;
                case 90:
                    $string = "ninety";
                    break;
                case $number < 100:
                    $prefix = translateToWords($number - $number % 10);
                    $suffix = translateToWords($number % 10);
                    $string = $prefix . "-" . $suffix;
                    break;
                // handles all number 100 to 999
                case $number < pow(10, 3):
                    // floor return a float not an integer
                    $prefix = translateToWords(intval(floor($number / pow(10, 2)))) . " hundred";
                    if ($number % pow(10, 2))
                        $suffix = " and " . translateToWords($number % pow(10, 2));
                    $string = $prefix . $suffix;
                    break;
                case $number < pow(10, 6):
                    // floor return a float not an integer
                    $prefix = translateToWords(intval(floor($number / pow(10, 3)))) . " thousand";
                    if ($number % pow(10, 3))
                        $suffix = translateToWords($number % pow(10, 3));
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 9):
                    // floor return a float not an integer
                    $prefix = translateToWords(intval(floor($number / pow(10, 6)))) . " million";
                    if ($number % pow(10, 6))
                        $suffix = translateToWords($number % pow(10, 6));
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 12):
                    // floor return a float not an integer
                    $prefix = translateToWords(intval(floor($number / pow(10, 9)))) . " billion";
                    if ($number % pow(10, 9))
                        $suffix = translateToWords($number % pow(10, 9));
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 15):
                    // floor return a float not an integer
                    $prefix = translateToWords(intval(floor($number / pow(10, 12)))) . " trillion";
                    if ($number % pow(10, 12))
                        $suffix = translateToWords($number % pow(10, 12));
                    $string = $prefix . " " . $suffix;
                    break;
                // Be careful not to pass default formatted numbers in the quadrillions+ into this function
                // Default formatting is float and causes errors
                case $number < pow(10, 18):
                    // floor return a float not an integer
                    $prefix = translateToWords(intval(floor($number / pow(10, 15)))) . " quadrillion";
                    if ($number % pow(10, 15))
                        $suffix = translateToWords($number % pow(10, 15));
                    $string = $prefix . " " . $suffix;
                    break;
            }
        } else {
            echo "ERROR with - $number<br/> Number must be an integer between -" . number_format($max_size, 0, ".", ",") . " and " . number_format($max_size, 0, ".", ",") . " exclussive.";
        }
        return $string;
    }

    function redirectTohttps() {
        if (isset($_SERVER["HTTPS"])) {
            $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location:$redirect");
        }
    }

    function loginRequired() {
        if (!isset(user()->id))
            app()->request->redirect(url('site/login'));
    }

    function date2Ind($str) {
        setlocale(LC_TIME, 'id_ID');
        $date = strftime("%d %B %Y", strtotime($str));
        if ($str == '0000-00-00')
            return '-';
        else
            return $date;
    }

    function smart_resize_image($file, $newName, $width = 0, $height = 0, $proportional = false, $quality = 100) {
        $output = 'file';

        if ($height <= 0 && $width <= 0)
            return false;
        if ($file === null)
            return false;

        # Setting defaults and meta
        $info = getimagesize($file);
        $image = '';
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;

        # Calculating proportionality
        if ($proportional) {
            if ($width == 0)
                $factor = $height / $height_old;
            elseif ($height == 0)
                $factor = $width / $width_old;
            else
                $factor = min($width / $width_old, $height / $height_old);
            $final_width = round($width_old * $factor);
            $final_height = round($height_old * $factor);
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }

        # Loading image to memory according to type
        switch ($info[2]) {
            case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_GIF: $image = imagecreatefromgif($file);
                break;
            case IMAGETYPE_PNG: $image = imagecreatefrompng($file);
                break;
            default: return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor($final_width, $final_height);
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


        # Taking care of original, if needed
//        if ($delete_original) {
//            if ($use_linux_commands)
//                exec('rm ' . $file);
//            else
//        unlink($file);
//        }
        # Preparing a method of providing result
        switch (strtolower($output)) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $newName;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination and image quality
        switch ($info[2]) {
            case IMAGETYPE_GIF: imagegif($image_resized, $output);
                break;
            case IMAGETYPE_JPEG: imagejpeg($image_resized, $output, $quality);
                break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int) ((0.9 * $quality) / 10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default: return false;
        }
        return true;
    }

}

?>
