<?php
/**
 * Created by PhpStorm.
 * User: vinhnguyen
 * Date: 12/8/2015
 * Time: 2:19 PM
 */
namespace console\models;

use Collator;
use keltstr\simplehtmldom\SimpleHTMLDom;
use vsoft\ad\models\AdCity;
use vsoft\ad\models\AdContactInfo;
use vsoft\ad\models\AdDistrict;
use vsoft\ad\models\AdImages;
use vsoft\ad\models\AdProduct;
use vsoft\ad\models\AdProductAdditionInfo;
use vsoft\ad\models\AdStreet;
use vsoft\ad\models\AdWard;
use Yii;
use yii\base\Component;
use yii\base\ErrorException;

class BatdongsanV2 extends Component
{
    const DOMAIN = 'http://batdongsan.com.vn';
    protected $domain = 'http://batdongsan.com.vn';
    protected $types = ['nha-dat-ban-quan-1','nha-dat-ban-quan-2','nha-dat-ban-quan-3','nha-dat-ban-quan-4','nha-dat-ban-quan-5','nha-dat-ban-quan-6',
        'nha-dat-ban-quan-7','nha-dat-ban-quan-8', 'nha-dat-ban-quan-9','nha-dat-ban-quan-10','nha-dat-ban-quan-11','nha-dat-ban-quan-12',
        'nha-dat-ban-binh-chanh','nha-dat-ban-binh-tan','nha-dat-ban-binh-thanh','nha-dat-ban-can-gio','nha-dat-ban-cu-chi','nha-dat-ban-go-vap',
        'nha-dat-ban-hoc-mon','nha-dat-ban-nha-be','nha-dat-ban-tan-binh','nha-dat-ban-tan-phu','nha-dat-ban-phu-nhuan','nha-dat-ban-thu-duc'];
    protected $time_start = 0;
    protected $time_end = 0;

    /**
     * @return mixed
     */
    public static function find()
    {
        return Yii::createObject(BatdongsanV2::className());
    }

    public function parse()
    {
        ob_start();
        $this->time_start = time();
        $this->getPages();
        $this->time_end = time();
        print_r("\nTime: ");
        print_r($this->time_end - $this->time_start);
    }

    public function setZeroCurrentPage(){
        foreach ($this->types as $key_type => $type) {
            $path_folder = Yii::getAlias('@console') . "/data/bds_html/{$type}/";
            $file = $path_folder."bds_log_{$type}.json";
            if(file_exists($file)){
                $file_log = $this->loadFileLog($type);
                if(!empty($file_log["current_page"])){
                    $file_log["current_page"] = 0;
                    $this->writeFileLog($type, $file_log);
                }
            }
        }
    }

    public function getPages()
    {
        $bds_log = $this->loadBdsLog();
        if(empty($bds_log["type"])){
            $bds_log["type"] = array();
        }
        $last_type = empty($bds_log["last_type_index"]) ? 0 : ($bds_log["last_type_index"] + 1);
        $count_type = count($this->types);

        if($last_type >= $count_type) {
            $bds_log["type"] = array();
            unset($bds_log["last_type_index"]);
            $last_type = 0;
            $this->writeBdsLog($bds_log);
            $this->setZeroCurrentPage();
        }

        foreach ($this->types as $key_type => $type) {
            if ($key_type >= $last_type) {
                $url = self::DOMAIN . '/' . $type;
                $page = $this->getUrlContent($url);
                if (!empty($page)) {
                    $html = SimpleHTMLDom::str_get_html($page, true, true, DEFAULT_TARGET_CHARSET, false);
                    $pagination = $html->find('.container-default .background-pager-right-controls a');
                    $count_page = count($pagination);
                    $last_page = (int)str_replace("/" . $type . "/p", "", $pagination[$count_page - 1]->href);
                    if ($count_page > 0) {
                        $log = $this->loadFileLog($type);
                        $current_page = empty($log["current_page"]) ? 1 : ($log["current_page"] + 1);
                        $current_page_add = $current_page + 4; // +4 => total page to run are 5.
                        if($current_page_add > $last_page)
                            $current_page_add = $last_page;

                        if ($current_page <= $last_page) {
                            for ($i = $current_page; $i <= $current_page_add; $i++) {
                                $log = $this->loadFileLog($type);
                                $sequence_id = empty($log["last_id"]) ? 0 : ($log["last_id"] + 1);
                                $list_return = $this->getListProject($type, $i, $sequence_id, $log);
                                if (!empty($list_return["data"])) {
                                    $list_return["data"]["current_page"] = $i;
                                    $this->writeFileLog($type, $list_return["data"]);
                                    print_r("\n{$type}-page " . $i . " done!\n");
                                }
                                sleep(1);
                                ob_flush();
                            }

                            if($current_page != $current_page_add){
                                break;
                            }

                        } else {
                            $this->writeFileLogFail($type, "\nPaging end: Current:$current_page_add , last:$last_page" . "\n");
                        }
                    } else {
                        echo "\nCannot find listing. End page!" . self::DOMAIN;
                        $this->writeFileLogFail($type, "\nCannot find listing: $url" . "\n");
                    }
                } else {
                    echo "\nCannot access in get pages of " . self::DOMAIN;
                    $this->writeFileLogFail($type, "\nCannot access: $url" . "\n");
                }

                if(!in_array($type, $bds_log["type"])) {
                    array_push($bds_log["type"], $type);
                }
                $bds_log["last_type_index"] = $key_type;
                $this->writeBdsLog($bds_log);
                print_r("\nTYPE: {$type} DONE!\n");
            }
        }
    }

    public function getListProject($type, $current_page, $sequence_id, $log)
    {
        $href = "/".$type."/p".$current_page;
        $page = $this->getUrlContent(self::DOMAIN . $href);
        if(!empty($page)) {
            $html = SimpleHTMLDom::str_get_html($page, true, true, DEFAULT_TARGET_CHARSET, false);
            $list = $html->find('div.p-title a');
            if (count($list) > 0) {
                // about 20 listing
                foreach ($list as $item) {
                    if (preg_match('/pr(\d+)/', self::DOMAIN . $item->href, $matches)) {
                        if(!empty($matches[1])){
                            $productId = $matches[1];
                        }
                    }
                    $checkExists = false;
                    if(!empty($productId) && !empty($log["files"])) {
                        $checkExists = in_array($productId, $log["files"]);
                    }

                    if ($checkExists == false) {
                        $res = $this->getProjectDetail($type, $item->href);
                        if (!empty($res)) {
                            $log["files"][$sequence_id] = $res;
                            $log["last_id"] = $sequence_id;
                            $sequence_id = $sequence_id + 1;
                        }
                    } else {
                        var_dump($productId);
                    }
                }
                return ['data' => $log];
            } else {
                echo "\nCannot find listing. End page!".self::DOMAIN;
                $this->writeFileLogFail($type, "\nCannot find listing: $href"."\n");
            }

        } else {
            echo "\nCannot access in get List Project of ".self::DOMAIN;
            $this->writeFileLogFail($type, "\nCannot access: $href"."\n");
        }
        return null;
    }

    public function getProjectDetail($type, $href)
    {
        $page = $this->getUrlContent(self::DOMAIN . $href);
        $matches = array();
        if (preg_match('/pr(\d+)/', self::DOMAIN . $href, $matches)) {
            if(!empty($matches[1])){
                $product_id = $matches[1];
            }
        }

        if(!empty($product_id)) {
                $path = Yii::getAlias('@console') . "/data/bds_html/{$type}/files/";
                if(!is_dir($path)){
                    mkdir($path , 0777, true);
                    echo "\nDirectory {$path} was created";
                }
                $res = $this->writeFileJson($path.$product_id, $page);
                if($res){
                    $this->writeFileLogUrlSuccess($type, self::DOMAIN.$href."\n");
                    return $product_id;
                } else {
                    return null;
                }
        }
        else {
            echo "\nError go to detail at " .self::DOMAIN.$href;
            $this->writeFileLogFail($type, "\nCannot find detail: ".self::DOMAIN.$href."\n");
        }
    }

    function getCityId($cityFile, $cityDB)
    {
        foreach ($cityDB as $obj) {
            $c = new Collator('vi_VN');
            $check = $c->compare(trim($cityFile), trim($obj->name));
            if ($check == 0) {
                return (int)$obj->id;
            }
        }
        return null;
    }

    function getDistrictId($districtFile, $districtDB, $city_id)
    {
        if(!empty($city_id)) {
            foreach ($districtDB as $obj) {
                $c = new Collator('vi_VN');
                $check = $c->compare(trim($districtFile), trim($obj->name));
                if ($check == 0 && $obj->city_id == $city_id) {
                    return (int)$obj->id;
                }
            }
        }
        return null;
    }

    function getWardId($_file, $_data, $_id)
    {
        if(!empty($_id)) {
            foreach ($_data as $obj) {
                preg_match('/'.trim($obj->name).'$/', trim($_file), $match);
                if (!empty($match[0]) && $obj->district_id == $_id) {
                    return (int)$obj->id;
                }
            }
        }
        return null;
    }

    function getStreetId($_file, $_data, $_id)
    {
        if(!empty($_id)) {
            foreach ($_data as $obj) {
                $a = preg_quote(trim($obj->name), '/'); //  / -> \/
                $b = preg_quote(trim($_file));
                preg_match('/'.$a.'$/', $b, $match);
                if (!empty($match[0]) && $obj->district_id == $_id) {
                    return (int)$obj->id;
                }

            }
        }
        return null;
    }

    function loadBdsLog(){
        $path_folder = Yii::getAlias('@console') . "/data/bds_html/";
        $path = $path_folder."bds_log.json";
        if(!is_dir($path_folder)){
            mkdir($path_folder , 0777, true);
            echo "\nDirectory {$path_folder} was created";
        }
        $data = null;
        if(file_exists($path))
            $data = file_get_contents($path);
        else
        {
            $this->writeFileJson($path, null);
            $data = file_get_contents($path);
        }

        if(!empty($data)){
            $data = json_decode($data, true);
            return $data;
        }
        else
            return null;
    }

    function writeBdsLog($log){
        $file_name = Yii::getAlias('@console') . "/data/bds_html/bds_log.json";
        $log_data = json_encode($log);
        $this->writeFileJson($file_name, $log_data);
    }

    function loadFileLog($type){
        $path_folder = Yii::getAlias('@console') . "/data/bds_html/{$type}/";
        $path = $path_folder."bds_log_{$type}.json";
        if(!is_dir($path_folder)){
            mkdir($path_folder , 0777, true);
            echo "\nDirectory {$path_folder} was created";
        }
        $data = null;
        if(file_exists($path))
            $data = file_get_contents($path);
        else
        {
            $this->writeFileJson($path, null);
            $data = file_get_contents($path);
        }

        if(!empty($data)){
            $data = json_decode($data, true);
            return $data;
        }
        else
            return null;
    }

    function writeFileLog($type, $log){
        $file_name = Yii::getAlias('@console') . "/data/bds_html/{$type}/bds_log_{$type}.json";
        $log_data = json_encode($log);
        $this->writeFileJson($file_name, $log_data);
    }


    function writeFileLogFail($type, $log){
        $file_name = Yii::getAlias('@console') . "/data/bds_html/{$type}/bds_log_fail";
        if(!file_exists($file_name)){
            fopen($file_name, "w");
        }
        if( strpos(file_get_contents($file_name),$log) === false) {
            $this->writeToFile($file_name, $log, 'a');
        }
    }

    function writeFileLogUrlSuccess($type, $log){
        $file_name = Yii::getAlias('@console') . "/data/bds_html/{$type}/bds_log_urls";
        if(!file_exists($file_name)){
            fopen($file_name, "w");
        }
        if( strpos(file_get_contents($file_name),$log) === false) {
            $this->writeToFile($file_name, $log, 'a');
        }
    }


    public function writeFileJson($filePath, $data)
    {
        $handle = fopen($filePath, 'w') or die('Cannot open file:  ' . $filePath);
        $int = fwrite($handle, $data);
        fclose($handle);
        return $int;
    }

    public function writeToFile($filePath, $data, $mode = 'a')
    {
        $handle = fopen($filePath, $mode) or die('Cannot open file:  ' . $filePath);
        $int = fwrite($handle, $data);
        fclose($handle);
        return $int;
    }

    public function readFileJson($filePath)
    {
        $handle = fopen($filePath, 'r') or die('Cannot open file:  ' . $filePath);
        if (filesize($filePath) > 0) {
            $data = fread($handle, filesize($filePath));
            return $data;
        } else return null;
    }

    function getUrlContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, self::DOMAIN . '/nha-dat-ban-tp-hcm/');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpcode >= 200 && $httpcode < 300) ? $data : null;
    }

    function loadImportLog($type){
        $path_folder = Yii::getAlias('@console') . "/data/bds_html/import/";
        if(!is_dir($path_folder)){
            mkdir($path_folder , 0777, true);
            echo "\nDirectory {$path_folder} was created";
        }
        $data = null;
        $path = $path_folder."bds_import_{$type}.json";
        if(file_exists($path))
            $data = file_get_contents($path);
        else
        {
            $this->writeFileJson($path, null);
            $data = file_get_contents($path);
        }

        if(!empty($data)){
            $data = json_decode($data, true);
            return $data;
        }
        else
            return null;
    }

    function writeImportLog($type, $log){
        $file_name = Yii::getAlias('@console') . "/data/bds_html/import/bds_import_{$type}.json";
        $log_data = json_encode($log);
        $this->writeFileJson($file_name, $log_data);
    }

    function loadBdsImportLog($filename){
        $path_folder = Yii::getAlias('@console') . "/data/bds_html/import/";
        if(!is_dir($path_folder)){
            mkdir($path_folder , 0777, true);
            echo "\nDirectory {$path_folder} was created";
        }
        $data = null;
        $path = $path_folder.$filename;
        if(file_exists($path))
            $data = file_get_contents($path);
        else
        {
            $this->writeFileJson($path, null);
            $data = file_get_contents($path);
        }

        if(!empty($data)){
            $data = json_decode($data, true);
            return $data;
        }
        else
            return null;
    }

    function writeBdsImportLog($filename, $log){
        $file_name = Yii::getAlias('@console') . "/data/bds_html/import/".$filename;
        $log_data = json_encode($log);
        $this->writeFileJson($file_name, $log_data);
    }

    public function getAddress($lat, $long){
        $api_key1 = 'AIzaSyCTwptkS584b_mcZWt0j_86ZFYLL0j-1Yw';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$long}&key={$api_key1}";
        $address = array();
        $response = @file_get_contents($url);
        if(!empty($response)){
            $results = json_decode($response, true);
            if(!empty($results["results"])) {
                if (!empty($results["results"][0]["address_components"])) {
                    $detail = $results["results"][0]["address_components"];
                    if ($detail[count($detail) - 1]["short_name"] == "VN") {
                        foreach ($detail as $d) {
                            if ($d["types"][0] == "street_number") {
                                $address["home_no"] = $d["long_name"];
                            } elseif ($d["types"][0] == "route") {
                                $address["street"] = $d["long_name"];
                            } elseif ($d["types"][0] == "sublocality_level_1") {
                                $address["ward"] = $d["long_name"];
                            } elseif ($d["types"][0] == "administrative_area_level_2") {
                                $address["district"] = $d["long_name"];
                            } elseif ($d["types"][0] == "administrative_area_level_1") {
                                $address["city"] = $d["long_name"];
                            }
                        }
                    }
                }
            } else {
                print_r("\nGoogle Map API limits at lat: {$lat} , long: {$long}");
            }
        }
        return $address;
    }

    public function importData()
    {
        $start_time = time();
        $insertCount = 0;
        $count_file = 1;

        $bds_import_log = $this->loadBdsImportLog("bds_import_log.json");
        if(empty($bds_import_log["type"])){
            $bds_import_log["type"] = array();
        }

        $last_type_import = empty($bds_import_log["last_type_index"]) ? 0 : ($bds_import_log["last_type_index"] + 1);
        $file_imported = empty($bds_import_log["file_imported"]) ? false : $bds_import_log["file_imported"];

        if(!$file_imported) {
            $columnNameArray = ['category_id', 'user_id', 'home_no',
                'city_id', 'district_id', 'ward_id', 'street_id',
                'type', 'content', 'area', 'price', 'lat', 'lng',
                'start_date', 'end_date', 'verified', 'created_at', 'source'];
            $bulkInsertArray = array();
            $imageArray = array();
            $infoArray = array();
            $contactArray = array();

            $cityData = AdCity::find()->all();
            $districtData = AdDistrict::find()->all();
            $wardData = AdWard::find()->all();
            $streetData = AdStreet::find()->all();
            $tableName = AdProduct::tableName();
            $break_type = false; // detect next type if it is false
            foreach ($this->types as $key_type => $type) {
                if ($key_type >= $last_type_import && !$break_type) {

                    $path = Yii::getAlias('@console') . "/data/bds_html/{$type}/files";
                    if (is_dir($path)) {
                        $log_import = $this->loadImportLog($type);
                        if (empty($log_import["files"]))
                            $log_import["files"] = array();

                        $files = scandir($path, 1);
                        $counter = count($files) - 2;
                        $last_file_index = $counter - 1;
                        if ($counter > 0) {
                            $filename = null;
                            for ($i = 0; $i <= $last_file_index; $i++) {
                                if ($count_file > 500) {
                                    $break_type = true;
                                    break;
                                }
                                $filename = $files[$i];
                                if (in_array($filename, $log_import["files"])) {
                                    continue;
                                } else {
                                    $filePath = $path . "/" . $filename;
                                    if (file_exists($filePath)) {
                                        print_r("\n".$count_file." - Prepare data {$type}: {$filename}");
                                        $value = $this->parseDetail($filePath);
                                        if(empty($value)){
                                            if (!in_array($filename, $log_import["files"]))
                                                array_push($log_import["files"], $filename);
                                            if (empty($log_import["NoContent"])) $log_import["NoContent"] = array();
                                            if(!in_array($filename, $log_import["NoContent"]))
                                                array_push($log_import["NoContent"], $filename);
                                            print_r(" Error: no content\n");
                                            continue;
                                        }

                                        if(count($value[$filename]["thumbs"]) <= 0){
                                            if (empty($log_import["NoImage"])) $log_import["NoImage"] = array();
                                            if(!in_array($filename, $log_import["NoImage"]))
                                                array_push($log_import["NoImage"], $filename);
//                                            print_r(" [No Image]");
                                        }
                                        $imageArray[$count_file] = $value[$filename]["thumbs"];
                                        $infoArray[$count_file] = $value[$filename]["info"];
                                        $contactArray[$count_file] = $value[$filename]["contact"];

                                        $city_id = $this->getCityId($value[$filename]["city"], $cityData);
                                        if (empty($city_id)) {
                                            if (!in_array($filename, $log_import["files"]))
                                                array_push($log_import["files"], $filename);
                                            if (empty($log_import["NoCity"])) $log_import["NoCity"] = array();
                                            if (!in_array($filename, $log_import["NoCity"]))
                                                array_push($log_import["NoCity"], $filename);
                                            print_r(" Error: no city\n");
                                            continue;
                                        }
                                        $district_id = $this->getDistrictId($value[$filename]["district"], $districtData, $city_id);
                                        if (empty($district_id)) {
                                            if (!in_array($filename, $log_import["files"]))
                                                array_push($log_import["files"], $filename);
                                            if (empty($log_import["NoDistrict"])) $log_import["NoDistrict"] = array();
                                            if (!in_array($filename, $log_import["NoDistrict"]))
                                                array_push($log_import["NoDistrict"], $filename);
                                            print_r(" Error: no district\n");
                                            continue;
                                        }

                                        $ward_id = $this->getWardId($value[$filename]["ward"], $wardData, $district_id);
                                        if (empty($ward_id)) {
                                            if (empty($log_import["NoWard"])) $log_import["NoWard"] = array();
                                            if (!in_array($filename, $log_import["NoWard"]))
                                                array_push($log_import["NoWard"], $filename);
//                                            print_r(" [No Ward]");
                                        }

                                        $street_id = $this->getStreetId($value[$filename]["street"], $streetData, $district_id);
                                        if (empty($street_id)) {
                                            if (empty($log_import["NoStreet"])) $log_import["NoStreet"] = array();
                                            if (!in_array($filename, $log_import["NoStreet"]))
                                                array_push($log_import["NoStreet"], $filename);
//                                            print_r(" [No Street]");
                                        }

                                        $area = $value[$filename]["dientich"];
                                        $price = $value[$filename]["price"];
                                        if ($price == 0) { // gia thoa thuan, gia trieu/m2 * 0 dien tich
                                            if ($area > 0) {
                                                if (empty($log_import["NoPrice"])) $log_import["NoPrice"] = array();
                                                if (!in_array($filename, $log_import["NoPrice"]))
                                                    array_push($log_import["NoPrice"], $filename);
                                                print_r(" Error: no price\n");
                                            } else {
                                                if (empty($log_import["NoArea"])) $log_import["NoArea"] = array();
                                                if (!in_array($filename, $log_import["NoArea"]))
                                                    array_push($log_import["NoArea"], $filename);
                                                print_r(" Error: no area\n");
                                            }
                                            if (!in_array($filename, $log_import["files"]))
                                                array_push($log_import["files"], $filename);
                                            continue;
                                        }

                                        $desc = $value[$filename]["description"];
                                        $content = null;
                                        if (!empty($desc)) {
                                            $content = strip_tags($desc, '<br>');
                                            $pos = strpos($content, 'Tìm kiếm theo từ khóa');
                                            if ($pos) {
                                                $content = substr($content, 0, $pos);
                                                $content = str_replace('Tìm kiếm theo từ khóa', '', $content);
                                            }
                                            $content = str_replace('<br/>', PHP_EOL, $content);
                                            $content = trim($content);
                                        }

                                        $record = [
                                            'category_id' => $value[$filename]["loai_tai_san"],
                                            'user_id' => null,
                                            'home_no' => $value[$filename]["home_no"],
                                            'city_id' => $city_id,
                                            'district_id' => $district_id,
                                            'ward_id' => $ward_id,
                                            'street_id' => $street_id,
                                            'type' => $value[$filename]["loai_giao_dich"],
                                            'content' => $content,
                                            'area' => $area,
                                            'price' => $price,
                                            'lat' => $value[$filename]["lat"],
                                            'lng' => $value[$filename]["lng"],
                                            'start_date' => $value[$filename]["start_date"],
                                            'end_date' => $value[$filename]["end_date"],
                                            'verified' => 1,
                                            'created_at' => $value[$filename]["start_date"],
                                            'source' => 1
                                        ];
                                        // source = 1 for Batdongsan.com.vn
                                        $bulkInsertArray[] = $record;

                                        print_r(" Added.\n");
                                        array_push($log_import["files"], $filename);
                                        $log_import["import_total"] = count($log_import["files"]);
                                        $log_import["import_time"] = date("d-m-Y H:i");
                                        $count_file++;
                                    }
                                }
                            } // end file loop
                            $this->writeImportLog($type, $log_import);
                            if ($break_type == false && count($bulkInsertArray) > 0) {
                                if (!in_array($type, $bds_import_log["type"])) {
                                    array_push($bds_import_log["type"], $type);
                                }
                                $bds_import_log["last_type_index"] = $key_type;
                                $this->writeBdsImportLog("bds_import_log.json", $bds_import_log);
                                print_r("\nADD: {$type} DONE!\n");
                            }
                        }
                    }
                }
            } // end types
            if (count($bulkInsertArray) > 0) {
                print_r("\nInsert data...");
                // below line insert all your record and return number of rows inserted
                $insertCount = Yii::$app->db->createCommand()
                    ->batchInsert($tableName, $columnNameArray, $bulkInsertArray)->execute();
                print_r(" DONE!");

                if ($insertCount > 0) {
                    $ad_image_columns = ['user_id', 'product_id', 'file_name', 'uploaded_at'];
                    $ad_info_columns = ['product_id', 'floor_no', 'room_no', 'toilet_no'];
                    $ad_contact_columns = ['product_id', 'name', 'phone', 'mobile', 'address'];

                    $bulkImage = array();
                    $bulkInfo = array();
                    $bulkContact = array();

                    $fromProductId = Yii::$app->db->getLastInsertID();
                    $toProductId = $fromProductId + $insertCount - 1;

                    $index = 1;
                    for ($i = $fromProductId; $i <= $toProductId; $i++) {
                        $ad_product = AdProduct::findOne($i);
                        if (!empty($ad_product)) {
                            if (count($imageArray) > 0) {
                                foreach ($imageArray[$index] as $imageValue) {
                                    if (!empty($imageValue)) {
                                        $imageRecord = [
                                            'user_id' => null,
                                            'product_id' => $i,
                                            'file_name' => $imageValue,
                                            'upload_at' => time()
                                        ];
                                        $bulkImage[] = $imageRecord;
                                    }
                                }
                            }

                            if (count($infoArray) > 0) {
                                $floor_no = empty($infoArray[$index]["Số tầng"]) == false ? trim(str_replace('(tầng)', '', $infoArray[$index]["Số tầng"])) : 0;
                                $room_no = empty($infoArray[$index]["Số phòng ngủ"]) == false ? trim(str_replace('(phòng)', '', $infoArray[$index]["Số phòng ngủ"])) : 0;
                                $toilet_no = empty($infoArray[$index]["Số toilet"]) == false ? trim($infoArray[$index]["Số toilet"]) : 0;
                                $infoRecord = [
                                    'product_id' => $i,
                                    'floor_no' => $floor_no,
                                    'room_no' => $room_no,
                                    'toilet_no' => $toilet_no
                                ];
                                $bulkInfo[] = $infoRecord;
                            }
                            if (count($contactArray) > 0) {
                                $name = empty($contactArray[$index]["Tên liên lạc"]) == false ? trim($contactArray[$index]["Tên liên lạc"]) : null;
                                $phone = empty($contactArray[$index]["Điện thoại"]) == false ? trim($contactArray[$index]["Điện thoại"]) : null;
                                $mobile = empty($contactArray[$index]["Mobile"]) == false ? trim($contactArray[$index]["Mobile"]) : null;
                                $address = empty($contactArray[$index]["Địa chỉ"]) == false ? trim($contactArray[$index]["Địa chỉ"]) : null;
                                $contactRecord = [
                                    'product_id' => $i,
                                    'name' => $name,
                                    'phone' => $phone,
                                    'mobile' => $mobile,
                                    'address' => $address
                                ];
                                $bulkContact[] = $contactRecord;
                            }
                            $index = $index + 1;
                        }
                    }

                    // execute image, info, contact
                    if (count($bulkImage) > 0) {
                        $imageCount = Yii::$app->db->createCommand()
                            ->batchInsert(AdImages::tableName(), $ad_image_columns, $bulkImage)
                            ->execute();
                        if ($imageCount > 0)
                            print_r("\nInser image done");
                    }
                    if (count($bulkInfo) > 0) {
                        $infoCount = Yii::$app->db->createCommand()
                            ->batchInsert(AdProductAdditionInfo::tableName(), $ad_info_columns, $bulkInfo)
                            ->execute();
                        if ($infoCount > 0)
                            print_r("\nInser product addition info done");
                    }
                    if (count($bulkContact) > 0) {
                        $contactCount = Yii::$app->db->createCommand()
                            ->batchInsert(AdContactInfo::tableName(), $ad_contact_columns, $bulkContact)
                            ->execute();
                        if ($contactCount > 0)
                            print_r("\nInser contact info done");
                    }
                } else {
                    print_r("\nCannot insert ad_product!!");
                }

                if (!$break_type) {
                    $bds_import_log["file_imported"] = true;
                    $this->writeBdsImportLog("bds_import_log.json",$bds_import_log);
                }
            }
        }

        print_r("\n\n------------------------------");
        print_r("\nFiles have been imported!\n");
        $end_time = time();
        print_r("\n"."Time: ");
        print_r($end_time-$start_time);
        print_r("s - Total Record: ". $insertCount);
    }

    public function importDataForTool()
    {
        $start_time = time();
        $insertCount = 0;
        $count_file = 1;

        $bds_import_log = $this->loadBdsImportLog("bds_import_log.json");
        if(empty($bds_import_log["type"])){
            $bds_import_log["type"] = array();
        }

        $last_type_import = empty($bds_import_log["last_type_index"]) ? 0 : ($bds_import_log["last_type_index"] + 1);
        $file_for_tool_imported = empty($bds_import_log["file_for_tool_imported"]) ? false : $bds_import_log["file_for_tool_imported"];

        if(!$file_for_tool_imported) {
            $columnNameArray = ['category_id', 'user_id', 'home_no',
                'city_id', 'district_id', 'ward_id', 'street_id',
                'type', 'content', 'area', 'price', 'price_type', 'lat', 'lng',
                'start_date', 'end_date', 'verified', 'created_at', 'source'];
            $bulkInsertArray = array();
            $imageArray = array();
            $infoArray = array();
            $contactArray = array();

            $cityData = AdCity::find()->all();
            $districtData = AdDistrict::find()->all();
            $wardData = AdWard::find()->all();
            $streetData = AdStreet::find()->all();
            $tableName = AdProduct::tableName();
            $break_type = false; // detect next type if it is false
            foreach ($this->types as $key_type => $type) {
                if ($key_type >= $last_type_import && !$break_type) {
                    $path = Yii::getAlias('@console') . "/data/bds_html/{$type}/files";
                    if (is_dir($path)) {
                        $log_import = $this->loadImportLog($type);
                        if (empty($log_import["files"]))
                            $log_import["files"] = array();

                        $files = scandir($path, 1);
                        $counter = count($files) - 2;
                        $last_file_index = $counter - 1;

                        if ($counter > 0) {
                            $filename = null;
                            for ($i = 0; $i <= $last_file_index; $i++){
                                if ($count_file > 1000) {
                                    $break_type = true;
                                    break;
                                }
                                $filename = $files[$i];
                                if (in_array($filename, $log_import["files"])) {
                                    continue;
                                } else {
                                    $filePath = $path . "/" . $filename;
                                    if (file_exists($filePath)) {
                                        print_r("\n".$count_file." {$type}: {$filename}");
                                        $value = $this->parseDetail($filePath);
                                        if(empty($value)){
                                            print_r(" Error: no content\n");
                                            continue;
                                        }

                                        $imageArray[$count_file] = $value[$filename]["thumbs"];
                                        $infoArray[$count_file] = $value[$filename]["info"];
                                        $contactArray[$count_file] = $value[$filename]["contact"];

                                        $city_id = $this->getCityId($value[$filename]["city"], $cityData);
                                        $district_id = $this->getDistrictId($value[$filename]["district"], $districtData, $city_id);
                                        $ward_id = $this->getWardId($value[$filename]["ward"], $wardData, $district_id);
                                        $street_id = $this->getStreetId($value[$filename]["street"], $streetData, $district_id);


                                        $area = $value[$filename]["dientich"];
                                        $price = $value[$filename]["price"];

                                        $desc = $value[$filename]["description"];
                                        $content = null;
                                        if (!empty($desc)) {
                                            $content = strip_tags($desc, '<br>');
                                            $pos = strpos($content, 'Tìm kiếm theo từ khóa');
                                            if ($pos) {
                                                $content = substr($content, 0, $pos);
                                                $content = str_replace('Tìm kiếm theo từ khóa', '', $content);
                                            }
                                            $content = str_replace('<br/>', PHP_EOL, $content);
                                            $content = trim($content);
                                        }

                                        $record = [
                                            'category_id' => $value[$filename]["loai_tai_san"],
                                            'user_id' => null,
                                            'home_no' => $value[$filename]["home_no"],
                                            'city_id' => $city_id,
                                            'district_id' => $district_id,
                                            'ward_id' => $ward_id,
                                            'street_id' => $street_id,
                                            'type' => $value[$filename]["loai_giao_dich"],
                                            'content' => $content,
                                            'area' => $area,
                                            'price' => $price,
                                            'price_type' => $price != -1 ? 1 : 0,
                                            'lat' => $value[$filename]["lat"],
                                            'lng' => $value[$filename]["lng"],
                                            'start_date' => $value[$filename]["start_date"],
                                            'end_date' => $value[$filename]["end_date"],
                                            'verified' => 1,
                                            'created_at' => $value[$filename]["start_date"],
                                            'source' => 1
                                        ];
                                        // source = 1 for Batdongsan.com.vn
                                        $bulkInsertArray[] = $record;

                                        print_r(" Added.\n");
                                        array_push($log_import["files"], $filename);
                                        $log_import["import_total"] = count($log_import["files"]);
                                        $log_import["import_time"] = date("d-m-Y H:i");
                                        $count_file++;
                                    }
                                }
                            } // end file loop
                            $this->writeImportLog($type, $log_import);
                            if ($break_type == false && count($bulkInsertArray) > 0) {
                                if (!in_array($type, $bds_import_log["type"])) {
                                    array_push($bds_import_log["type"], $type);
                                }
                                $bds_import_log["last_type_index"] = $key_type;
                                $this->writeBdsImportLog("bds_import_log.json", $bds_import_log);
                                print_r("\nADD: {$type} DONE!\n");
                            }
                        }
                    }
                }
            } // end types
            if (count($bulkInsertArray) > 0) {
                print_r("\nInsert data...");
                // below line insert all your record and return number of rows inserted
                $insertCount = Yii::$app->db->createCommand()
                    ->batchInsert($tableName, $columnNameArray, $bulkInsertArray)->execute();
                print_r(" DONE!");

                if ($insertCount > 0) {
                    $ad_image_columns = ['user_id', 'product_id', 'file_name', 'uploaded_at'];
                    $ad_info_columns = ['product_id', 'facade_width', 'land_width', 'home_direction', 'facade_direction', 'floor_no', 'room_no', 'toilet_no', 'interior'];
                    $ad_contact_columns = ['product_id', 'name', 'phone', 'mobile', 'address'];

                    $bulkImage = array();
                    $bulkInfo = array();
                    $bulkContact = array();

                    $fromProductId = Yii::$app->db->getLastInsertID();
                    $toProductId = $fromProductId + $insertCount - 1;

                    $index = 1;
                    for ($i = $fromProductId; $i <= $toProductId; $i++) {
                        $ad_product = AdProduct::findOne($i);
                        if (!empty($ad_product)) {
                            if (count($imageArray) > 0) {
                                foreach ($imageArray[$index] as $imageValue) {
                                    if (!empty($imageValue)) {
                                        $imageRecord = [
                                            'user_id' => null,
                                            'product_id' => $i,
                                            'file_name' => $imageValue,
                                            'upload_at' => time()
                                        ];
                                        $bulkImage[] = $imageRecord;
                                    }
                                }
                            }

                            if (count($infoArray) > 0) {
                                $facade_width = empty($infoArray[$index]["Mặt tiền"]) == false ? trim($infoArray[$index]["Mặt tiền"]) : null;
                                $land_width = empty($infoArray[$index]["Đường vào"]) == false ? trim($infoArray[$index]["Đường vào"]) : null;
                                $home_direction = empty($infoArray[$index]["direction"]) == false ? trim($infoArray[$index]["direction"]) : null;
                                $facade_direction = null;
                                $floor_no = empty($infoArray[$index]["Số tầng"]) == false ? trim(str_replace('(tầng)', '', $infoArray[$index]["Số tầng"])) : 0;
                                $room_no = empty($infoArray[$index]["Số phòng ngủ"]) == false ? trim(str_replace('(phòng)', '', $infoArray[$index]["Số phòng ngủ"])) : 0;
                                $toilet_no = empty($infoArray[$index]["Số toilet"]) == false ? trim($infoArray[$index]["Số toilet"]) : 0;
                                $interior = empty($infoArray[$index]["Nội thất"]) == false ? trim($infoArray[$index]["Nội thất"]) : null;
                                $infoRecord = [
                                    'product_id' => $i,
                                    'facade_width' => $facade_width,
                                    'land_width' => $land_width,
                                    'home_direction' => $home_direction,
                                    'facade_direction' => $facade_direction,
                                    'floor_no' => $floor_no,
                                    'room_no' => $room_no,
                                    'toilet_no' => $toilet_no,
                                    'interior' => $interior
                                ];
                                $bulkInfo[] = $infoRecord;
                            }
                            if (count($contactArray) > 0) {
                                $name = empty($contactArray[$index]["Tên liên lạc"]) == false ? trim($contactArray[$index]["Tên liên lạc"]) : null;
                                $phone = empty($contactArray[$index]["Điện thoại"]) == false ? trim($contactArray[$index]["Điện thoại"]) : null;
                                $mobile = empty($contactArray[$index]["Mobile"]) == false ? trim($contactArray[$index]["Mobile"]) : null;
                                $address = empty($contactArray[$index]["Địa chỉ"]) == false ? trim($contactArray[$index]["Địa chỉ"]) : null;
                                $contactRecord = [
                                    'product_id' => $i,
                                    'name' => $name,
                                    'phone' => $phone,
                                    'mobile' => $mobile == null ? $phone : $mobile,
                                    'address' => $address
                                ];
                                $bulkContact[] = $contactRecord;
                            }
                            $index = $index + 1;
                        }
                    }

                    // execute image, info, contact
                    if (count($bulkImage) > 0) {
                        $imageCount = Yii::$app->db->createCommand()
                            ->batchInsert(AdImages::tableName(), $ad_image_columns, $bulkImage)
                            ->execute();
                        if ($imageCount > 0)
                            print_r("\nInser image done");
                    }
                    if (count($bulkInfo) > 0) {
                        $infoCount = Yii::$app->db->createCommand()
                            ->batchInsert(AdProductAdditionInfo::tableName(), $ad_info_columns, $bulkInfo)
                            ->execute();
                        if ($infoCount > 0)
                            print_r("\nInser product addition info done");
                    }
                    if (count($bulkContact) > 0) {
                        $contactCount = Yii::$app->db->createCommand()
                            ->batchInsert(AdContactInfo::tableName(), $ad_contact_columns, $bulkContact)
                            ->execute();
                        if ($contactCount > 0)
                            print_r("\nInser contact info done");
                    }
                } else {
                    print_r("\nCannot insert ad_product!!");
                }

                if (!$break_type) {
                    $bds_import_log["file_for_tool_imported"] = true;
                    $this->writeBdsImportLog("bds_import_log.json",$bds_import_log);
                }
            }
        }

        print_r("\n\n------------------------------");
        print_r("\nFiles have been imported!\n");
        $end_time = time();
        print_r("\n"."Time: ");
        print_r($end_time-$start_time);
        print_r("s - Total Record: ". $insertCount);
    }

    public function parseDetail($filename)
    {
        $json = array();
        $page = file_get_contents($filename);
        if(empty($page))
            return null;
        $detail = SimpleHTMLDom::str_get_html($page, true, true, DEFAULT_TARGET_CHARSET, false);
        if (!empty($detail)) {
//                $title = $detail->find('h1', 0)->innertext;
            $href = $detail->find('#form1', 0)->action;
            $lat = $detail->find('#hdLat', 0)->value;
            $long = $detail->find('#hdLong', 0)->value;

            $product_id = $detail->find('.pm-content', 0)->cid;
            $content = $detail->find('.pm-content', 0)->innertext;

            $dientich = trim($detail->find('.gia-title', 1)->plaintext);
            $dt = 0;
            if (strpos($dientich, 'm²')) {
                $dientich = str_replace('m²', '', $dientich);
                $dientich = str_replace('Diện tích:', '', $dientich);
                $dientich = trim($dientich);
                $dt = $dientich;
            }

            $gia = trim($detail->find('.gia-title', 0)->plaintext);
            $price = 0;
            if (strpos($gia, ' triệu')) {
                $gia = str_replace('Giá:', '', $gia);
                if (strpos($gia, ' triệu/m²')) {
                    $gia = str_replace(' triệu/m²&nbsp;', '', $gia);
                    $gia = $gia * $dt;
                }
                else
                    $gia = str_replace(' triệu&nbsp;', '', $gia);

                $gia = trim($gia);
                $price = $gia * 1000000;
            } else if (strpos($gia, ' tỷ')) {
                $gia = str_replace('Giá:', '', $gia);
                $gia = str_replace(' tỷ&nbsp;', '', $gia);
                $gia = trim($gia);
                $price = $gia * 1000000000;
            } else
                $price = -1;

            $imgs = $detail->find('.pm-middle-content .img-map #thumbs li img');
            $thumbs = array();
            if (count($imgs) > 0) {
                foreach ($imgs as $img) {
                    $img_link = str_replace('80x60', '745x510', $img->src);
                    array_push($thumbs, $img_link);
                }
            }

            $left_detail = $detail->find('.pm-content-detail .left-detail', 0);
            $div_info = $left_detail->find('div div');
            $left = '';
            $city = null;
            $district = null;
            $ward = null;
            $street = null;
            $home_no = null;
            $startdate = time();
            $endate = time();
            $loai_tai_san = 6;
            $arr_info = [];
            if (count($div_info) > 0) {
                foreach ($div_info as $div) {
                    $class = $div->class;
                    if (!(empty($class))) {
                        if ($class == 'left')
                            $left = trim($div->innertext);
                        else if ($class == 'right') {
                            if (array_key_exists($left, $arr_info)) {
                                $left = $left . '_1';
                            }
                            $arr_info[$left] = trim($div->plaintext);
                        }
                    }
                }
            }

            if (count($arr_info) > 0) {
                // set address with link emailregister
                $emailregister = trim($detail->find('#emailregister', 0)->href);
                if(!empty($emailregister)) {
                    $emailregister = substr($emailregister, strpos($emailregister, "cityCode="), strlen($emailregister) - 1);
//                    print_r($emailregister);
                    $address = explode("&amp;", $emailregister);
                    if (count($address) > 8) {
                        $divCityOptions = $detail->find('#divCityOptions ul li');
                        $divDistrictOptions = $detail->find('#divDistrictOptions ul li');
                        $divWardOptions = $detail->find('#divWardOptions ul li');
                        $divStreetOptions = $detail->find('#divStreetOptions ul li');

                        foreach ($address as $val) {
                            if ($this->beginWith($val, "cityCode=")) {
                                $cityCode = str_replace("cityCode=", "", $val);
                                if(!empty($cityCode)) {
                                    foreach ($divCityOptions as $cityValue) {
                                        if ($cityCode == $cityValue->vl) {
                                            $city = $cityValue->plaintext;
                                            break;
                                        }
                                    }
                                }
                            } elseif ($this->beginWith($val, "distId=")) {
                                $d_id = (string)str_replace("distId=", "", $val);
                                if($d_id != "0") {
                                    foreach ($divDistrictOptions as $districtValue) {
                                        if ($d_id == $districtValue->vl) {
                                            $district = $districtValue->plaintext;
                                            break;
                                        }
                                    }
                                }
                            } elseif ($this->beginWith($val, "wardId=")) {
                                $w_id = (string)str_replace("wardId=", "", $val);
                                if($w_id != "0") {
                                    foreach ($divWardOptions as $wardValue) {
                                        if ($w_id == $wardValue->vl) {
                                            $ward = $wardValue->plaintext;
                                            break;
                                        }
                                    }
                                }
                            } elseif ($this->beginWith($val, "streetId=")) {
                                $s_id = (string)str_replace("streetId=", "", $val);
                                if($s_id != "0") {
                                    foreach ($divStreetOptions as $streetValue) {
                                        if ($s_id === $streetValue->vl) {
                                            $street = $streetValue->plaintext;
                                            break;
                                        }
                                    }
                                }
                            }  elseif ($this->beginWith($val, "direction=")) {
                                $direction_id = (int)str_replace("direction=", "", $val);
                                $arr_info["direction"] = $direction_id;
                            }
                        } // end for address
                    }
                }
                // truong hop ko co city hoac district
                if(empty($city) || empty($district)){
                    if (!empty($arr_info["Địa chỉ"])) {
                        $address = mb_split(',', $arr_info["Địa chỉ"]);
                        $count_address = count($address);
                        if ($count_address >= 3) {
                            $city = !empty($address[$count_address - 1]) ? $address[$count_address - 1] : null;
                            $district = !empty($address[$count_address - 2]) ? $address[$count_address - 2] : null;
                        }
                    }
                }

                $startdate = empty($arr_info["Ngày đăng tin"]) ? time() : trim($arr_info["Ngày đăng tin"]);
                $startdate = strtotime($startdate);

                $endate = empty($arr_info["Ngày hết hạn"]) ? time() : trim($arr_info["Ngày hết hạn"]);
                $endate = strtotime($endate);

                $loai_tin = empty($arr_info["Loại tin rao"]) ? "Bán căn hộ chung cư" : trim($arr_info["Loại tin rao"]);

                if ($this->beginWith($loai_tin, "Bán căn hộ chung cư")) {
                    $loai_tai_san = 6;
                } else if ($this->beginWith($loai_tin, "Bán nhà")) {
                    $loai_tai_san = 7;
                } else if ($this->beginWith($loai_tin, "Bán đất")) {
                    $loai_tai_san = 10;
                }
            }

            $contact = $detail->find('.pm-content-detail #divCustomerInfo', 0);
            $div_contact = $contact->find('div.right-content div');
            $right = '';
            $arr_contact = [];
            if (count($div_contact) > 0) {
                foreach ($div_contact as $div) {
                    $class = $div->class;
                    if (!(empty($class))) {
                        if (strpos($class, 'left') == true) {
                            $right = $div->plaintext;
                            $right = trim($right);
                        } else if ($class == 'right') {
                            if (array_key_exists($right, $arr_contact)) {
                                $right = $right . '_1';
                            }
                            $value = $div->innertext;
                            $arr_contact[$right] = trim($value);
                        }
                    }
                }
            }
            $json[$product_id] = [
                'lat' => trim($lat),
                'lng' => trim($long),
                'description' => trim($content),
                'thumbs' => $thumbs,
                'info' => $arr_info,
                'contact' => $arr_contact,
                'city' => $city,
                'district' => $district,
                'ward' => $ward,
                'street' => $street,
                'home_no' => $home_no,
                'loai_tai_san' => $loai_tai_san,
                'loai_giao_dich' => 1,
                'price' => $price,
                'dientich' => $dt,
                'start_date' => $startdate,
                'end_date' => $endate,
//                'link' => self::DOMAIN . $href
            ];
        }
        return $json;
    }

    function beginWith($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    public function updateData(){
        $products = AdProduct::find()->where(['ward_id' => null])->andWhere(['verified' => 1])->all();
        if(count($products) > 0){
//            $cityData = AdCity::find()->all();
//            $districtData = AdDistrict::find()->all();
            $wardData = AdWard::find()->all();
            $streetData = AdStreet::find()->all();
            $log_update = $this->loadBdsImportLog("bds_update_log.json");
            if(empty($log_update["pids"])) $log_update["pids"] = array();
            $i = 1;
            foreach($products as $product){
                if($i > 500)
                    break;
//                if(in_array($product->id, $log_update["pids"]))
//                    continue;
                $lat = $product->lat;
                $lng = $product->lng;
                $address = $this->getAddress($lat, $lng);
                if (count($address) > 0) {
                    if (empty($address["ward"])) {
                        $product->verified = 0;
                        if(empty($log_update["NoWard"])) $log_update["NoWard"] = array();
                        if(!in_array($product->id, $log_update["NoWard"]))
                            array_push($log_update["NoWard"], $product->id);
                        print_r("\nNot verified ward: {$product->id} and {$lat}, {$lng}");
                        continue;
                    }

                    if (!empty($address["street"])) {
                        $street_id = $this->getStreetId($address["street"], $streetData, $product->district_id);
                        $product->street_id = $street_id;
                    }

                    if(!empty($address["home_no"]))
                        $product->home_no = $address["home_no"];

                    $ward_id = $this->getWardId($address["ward"], $wardData, $product->district_id);
                    if(empty($ward_id)){
                        if(empty($log_update["NoWard"])) $log_update["NoWard"] = array();
                        if(!in_array($product->id, $log_update["NoWard"]))
                            array_push($log_update["NoWard"], $product->id);
                        print_r("\nNot found ward in DB: {$product->id} and {$lat}, {$lng}");
                        continue;
                    } else {
                        $product->ward_id = $ward_id;
                        if($product->update(false))
                            print_r("\n{$i} - updated id: {$product->id}");
                        else
                            print_r("\n{$i} - failed id: {$product->id}");
                    }
                }
                if(!in_array($product->id, $log_update["pids"]))
                    array_push($log_update["pids"], $product->id);
                $log_update["total-update"] = count($log_update["pids"]);
                $this->writeBdsImportLog("bds_update_log.json", $log_update);
                $i++;
                sleep(2);
            } // end for products loop
        }
        else {
            print_r("\nAll products have been updated");
        }
    }

    function loadLog($path, $filename){
        $filename = $path.$filename;
        if(!is_dir($path)){
            mkdir($path , 0777, true);
            echo "\nDirectory {$path} was created";
        }
        $data = null;
        if(file_exists($filename))
            $data = file_get_contents($filename);
        else
        {
            $this->writeFileJson($filename, null);
            $data = file_get_contents($filename);
        }

        if(!empty($data)){
            $data = json_decode($data, true);
            return $data;
        }
        else
            return null;
    }

    function writeLog($log, $path, $filename){
        $file_name = $path.$filename;
        $log_data = json_encode($log);
        $this->writeFileJson($file_name, $log_data);
    }

    public function getAgentId($href){
        $lastIndex = strripos($href, '-')+1;
        $result = substr($href, $lastIndex);
        return $result;
    }

    public function checkProductExists($path, $product_id, $mobile, $current_page){
        if(!is_dir($path)){
            mkdir($path , 0777, true);
            echo "\nDirectory {$path} was created";
        }
        $filename = $path.$current_page."-".$product_id."-".$mobile;;
        return file_exists($filename);
    }

    public function getAgents(){
        $path = Yii::getAlias('@console') . "/data/bds_html/agents/";
        $file_log = "agent_log.json";
        $log = $this->loadLog($path, $file_log);
        $current_page = empty($log["current_page"]) ? 1 : ($log["current_page"] + 1);
        $url = "http://batdongsan.com.vn/nha-moi-gioi/p{$current_page}";
        $page = $this->getUrlContent($url);
        if(!empty($page)){
            print_r("\nGoto: {$url}\n");
            $html = SimpleHTMLDom::str_get_html($page, true, true, DEFAULT_TARGET_CHARSET, false);
            $list_page = $html->find('.ttmgl');
            if(count($list_page)){
                foreach ($list_page as $p) {
                    $a = $p->find('.tenmg a', 0);
                    $href = $a->href;
                    if (!empty($href)) {
                        $product_id = $this->getAgentId($href);
                        if(empty($product_id))
                            continue;
                        $_info = $p->find('.ttmg div');
                        $broker_info = array();
                        $left = '';
                        foreach($_info as $div){
                            $class = trim($div->class);
                            if (!(empty($class))) {
                                if ($class == 'left')
                                    $left = trim($div->innertext);
                                else if ($class == 'right') {
                                    $broker_info[$left] = trim($div->plaintext);
                                }
                            }
                        }
                        $mobile = empty($broker_info["Di động:"]) ? null : ($broker_info["Di động:"] == "Đang cập nhật" ? null : $broker_info["Di động:"]);
                        // check agent exists in folder
                        if($this->checkProductExists($path."files/", $product_id, $mobile, $current_page)){
                            print_r("\nduplicate: ".$current_page."-".$product_id."-".$mobile);
                            continue;
                        } else {
                            $this->getAgentDetail($path."files/", $product_id, $href, $broker_info, $current_page);

                        }
                    }
                }
                $log["current_page"] = $current_page;
                $this->writeLog($log, $path, $file_log);
                print_r("\nPage {$current_page} done.\n");
            }

            $pagination = $html->find('.container-default .pager-block a');
            $total_page = (int)str_replace("/nha-moi-gioi/p", "", $pagination[count($pagination) - 1]->href);
            if($total_page > 0){
                $current_page_add = $current_page + 9; // +4 => total page to run are 10.
                if($current_page_add > $total_page)
                    $current_page_add = $total_page;

                if($current_page > $total_page)
                    $current_page = 0;

                for($i=$current_page+1; $i<=$current_page_add; $i++){
                    $url = "http://batdongsan.com.vn/nha-moi-gioi/p{$i}";
                    $page = $this->getUrlContent($url);
                    if(!empty($page)){
                        print_r("\nGoto: {$url}\n");
                        $html_page = SimpleHTMLDom::str_get_html($page, true, true, DEFAULT_TARGET_CHARSET, false);
                        $list_page = $html_page->find('.ttmgl');
                        if(count($list_page)){
                            foreach ($list_page as $p) {
                                $a = $p->find('.tenmg a', 0);
                                $href = $a->href;
                                if (!empty($href)) {
                                    $product_id = $this->getAgentId($href);
                                    if(empty($product_id))
                                        continue;

                                    $_info = $p->find('.ttmg div');
                                    $broker_info = array();
                                    $left = '';
                                    foreach($_info as $div){
                                        $class = trim($div->class);
                                        if (!(empty($class))) {
                                            if ($class == 'left')
                                                $left = trim($div->innertext);
                                            else if ($class == 'right') {
                                                $broker_info[$left] = trim($div->plaintext);
                                            }
                                        }
                                    }
                                    $mobile = empty($broker_info["Di động:"]) ? null : ($broker_info["Di động:"] == "Đang cập nhật" ? null : $broker_info["Di động:"]);
                                    // check agent exists in folder
                                    if($this->checkProductExists($path."files/", $product_id, $mobile, $i)){
                                        print_r("\n".$i."-".$product_id."-".$mobile." duplicated.");
                                        continue;
                                    } else {

                                        $this->getAgentDetail($path."files/", $product_id, $href, $broker_info, $i);
                                    }
                                }
                            }
                            $log["current_page"] = $i;
                            $this->writeLog($log, $path, $file_log);
                            print_r("\nPage {$i} done.\n");
                            sleep(1);
                        }
                    } else {
                        print_r("\nCannot access {$url}");
                    }
                }
            } else {
                print_r("Why not get total page?");
            }
        }
    }

    public function getAgentDetail($path, $product_id, $href, $broker_info, $current_page){
        $page = $this->getUrlContent($this->domain.$href);
        if(!empty($page)){
            if(!is_dir($path)){
                mkdir($path , 0777, true);
                echo "\nDirectory {$path} was created";
            }
            $mobile = empty($broker_info["Di động:"]) ? null : ($broker_info["Di động:"] == "Đang cập nhật" ? null : $broker_info["Di động:"]);
            $filename = $current_page."-".$product_id."-".$mobile;
            if($this->writeFileJson($path.$filename, $page) > 0){
                return true;
            }
        } else {
            print_r("\nCannot access {$href}");
        }
        return false;
    }

    public function importAgent(){
        $path = Yii::getAlias('@console') . "/data/bds_html/agents/";
        $file_log = "import_agent_log.json";
        $log_import = $this->loadLog($path, $file_log);
        if (empty($log_import["files"]))
            $log_import["files"] = array();
        $agent_imported = empty($log_import["agent_imported"]) ? false : $log_import["agent_imported"];

        if(!$agent_imported) {
            $columnNameArray = ['name', 'address', 'mobile', 'phone', 'fax', 'email', 'website', 'rating', 'working_area', 'source', 'type', 'tax_code', 'updated_at'];
            $bulkInsertArray = array();
            $files = scandir($path."/files", 1);
            $counter = count($files) - 2;
            $last_file_index = $counter - 1;
            if ($counter > 0) {
                $filename = null;
                $count_file = 1;
                for ($i = 0; $i <= $last_file_index; $i++) {
                    if ($count_file > 400) {
                        break;
                    }
                    $filename = $files[$i];
                    if (in_array($filename, $log_import["files"])) {
                        continue;
                    } else {
                        $filePath = $path ."files/". $filename;
                        if (file_exists($filePath)) {
                            print_r("\n" . $count_file . " - {$filename}");
                            $value = $this->parseAgentDetail($path, $filename);
                            if (empty($value)) {
                                print_r(" Error: no content\n");
                                continue;
                            }

                            $record = [
                                'name' => $value["name"],
                                'address' => $value["name"],
                                'mobile' => $value["mobile"],
                                'phone' => $value["phone"],
                                'fax' => $value["fax"],
                                'email' => $value["email"],
                                'website' => $value["website"],
                                'rating' => $value["rating"],
                                'working_area' => $value["working_area"],
                                'source' => $value["source"],
                                'type' => $value["type"],
                                'tax_code' => $value["tax_code"],
                                'updated_at' => $value["updated_at"]
                            ];
                            // source = 1 for Batdongsan.com.vn
                            $bulkInsertArray[] = $record;

                            print_r(" Added.\n");
                            array_push($log_import["files"], $filename);
                            $log_import["import_total"] = count($log_import["files"]);
                            $log_import["import_time"] = date("d-m-Y H:i");
                            $count_file++;
                        }
                    }
                } // end file loop
                $this->writeLog($log_import, $path, $file_log);
                if (count($bulkInsertArray) > 0) {
                    print_r("\nInsert data...");
                    // below line insert all your record and return number of rows inserted
                    $insertCount = Yii::$app->db->createCommand()
                        ->batchInsert("agent", $columnNameArray, $bulkInsertArray)->execute();
                    print_r(" DONE!");
                }
            }
        }
    }

    public function parseAgentDetail($path, $filename){
        $json = array();
        $filePath = $path."files/".$filename;
        $page = file_get_contents($filePath);
        if(empty($page))
            return null;
        $detail = SimpleHTMLDom::str_get_html($page, true, true, DEFAULT_TARGET_CHARSET, false);
        if (!empty($detail)) {
            $name_obj = $detail->find('.broker-detail h1', 0);
            $name = null;
            if(!empty($name_obj)){
                $name = $name_obj->plaintext;
            } else {
                return null;
            }
            $rating = 0;

            $broker_info = array();
            $left = '';
            $broker_detail = $detail->find('.broker-detail .ttmg div');
            if(empty($broker_detail)) return null;
            foreach ($broker_detail as $div) {
                $class = $div->class;
                if (!(empty($class))) {
                    if ($class == 'left')
                        $left = trim($div->innertext);
                    else if ($class == 'right') {
                        $broker_info[$left] = trim(str_replace(":","",$div->innertext));
                    }
                }
            }

            $address = empty($broker_info["Địa chỉ"]) ? null : $broker_info["Địa chỉ"];
            $filename_array = explode("-",$filename);
            $mobile = null;
            if(count($filename_array) > 0)
                $mobile = $filename_array[count($filename_array)-1];

            $dt = empty($broker_info["ĐT"]) ? null : ($broker_info["ĐT"] == "Đang cập nhật" ? null : $broker_info["ĐT"]);
            $dienthoai = empty($broker_info["Điện thoại"]) ? null : ($broker_info["Điện thoại"] == "Đang cập nhật" ? null : $broker_info["Điện thoại"]);
            $fax = empty($broker_info["Fax"]) ? null : ($broker_info["Fax"] == "Đang cập nhật" ? null : $broker_info["Fax"]);
            $str_email = empty($broker_info["Email"]) ? null : ($broker_info["Email"] == "Đang cập nhật" ? null : $broker_info["Email"]);
            $email = null;
            if(!empty($str_email)) {
                $email = substr($str_email, strpos($str_email, "var attr = '"));
                $email = str_replace("var attr = '", "", $email);
                $email = substr($email, 0, strpos($email, "var txt ="));
                $email = str_replace("';", "", $email);
                $email = html_entity_decode($email);
            }
            $web = empty($broker_info["Website"]) ? null : ($broker_info["Website"] == "Đang cập nhật" ? null : $broker_info["Website"]);
            $type_ = $detail->find('.introtitle', 0);
            $type = null;
            if(!empty($type_) && $type_->plaintext == "Khu vực công ty môi giới")
                $type = 1;
            else
                $type = 2;
            $working_list = $detail->find('.ltrAreaIntro ul li');
            $working = null;
            if(!empty($working_list)){
                foreach($working_list as $place){
                    $working .= " ".trim($place->plaintext).";";
                }
                $working = rtrim($working, ";");
            }

            $tax = empty($broker_info["Mã số thuế"]) ? null : ($broker_info["Mã số thuế"] == "Đang cập nhật" ? null : $broker_info["Mã số thuế"]);

            $json = [
                'name' => trim($name), 'address' => $address,
                'mobile' => $mobile,
                'phone' => empty($dt) ? $dienthoai : $dt,
                'fax' => $fax,
                'email' => $email,
                'website' => $web,
                'rating' => $rating,
                'working_area' => $working,
                'source' => 1,
                'type' => $type,
                'tax_code' => $tax, 'updated_at' => time()
            ];
            return $json;
        }
    }

}