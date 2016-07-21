<?php

/**
 * Created by PhpStorm.
 * User: Herman
 * Date: 20/07/2016
 * Time: 13:32
 */
class FakeDataGeneratorController extends Controller
{

    private $generatedData = [];
    private $unique = [];

    public function __construct()
    {
        parent::__construct();

        $this->loadModel("ClassManager");
    }

    public function index() {

        $this->loadModel("ClassManager");
        $data = $this->model->describeDatabase(Session::get("selected_db"));
        //var_dump($data);
        $this->view->DBFields = $this->sortTables($data);
        $this->view->render("fake/index");
    }

    public function generate() {
        //Controller::debug($_POST);
        //die;
        $tables = $_POST['tables'];
        $type = $_POST['data_type'];
        $number = $_POST['number'];
        $generateId = $_POST['use_id'];
        //$round = $_POST['Tour'];
        $round = 0;

        $global_tables = [];
        foreach ($tables as $key => $table) {
            $this->proccessMetaData($table, $type, $number, $generateId, $round, $global_tables);
        }

        $fp = fopen(GENERATED_FAKE_DATA."_fake.".$type, "w+");

        foreach ($global_tables as $key => $table) {
            //++$round;
            $this->generateSimple($table, $type, $number, $generateId, $round, $fp);
        }
    }

    private function proccessMetaData($table, $type, $number, $generateId, $round, &$tables)
    {
        $desc = $this->model->getForeingTables($table);
        //$this->generateSimple($table, $type, $number, $generateId, $round);
        if (count($desc) > 0) {
            //echo "Complex ". count($desc) . PHP_EOL;

            foreach ($desc as $key => $value) {
                $desc2 = $this->model->getForeingTables($desc[$key]['Foreign_table']);
                if(count($desc2) == 0) {
                    if(!in_array($desc[$key]['Foreign_table'], $tables))
                        $tables[] = $desc[$key]['Foreign_table'];
                } else {
                    if($desc[$key]['Foreign_table'] != $table) {
                        $this->proccessMetaData($desc[$key]['Foreign_table'], $type, $number, $generateId, $round, $tables);
                        if(!in_array($desc[$key]['Foreign_table'], $tables))
                            $tables[] = $desc[$key]['Foreign_table'];
                    }
                }
            }
        }

        if(!in_array($table, $tables))
            $tables[] = $table;
    }

    private function generateSimple($table, $type, $number, $generateId, $round, $fp)
    {
        //echo "Generate " . $number ." data of type ".$type . " for ".$table ."<br>";
        $this->generateFakeData($table, $number, $type, $generateId, $round,$fp);
    }

    private function generateFakeData($table, $size, $type, $generateId, $round,$fp)
    {
        if(!is_dir(GENERATED_FAKE_DATA))
        {
            mkdir(GENERATED_FAKE_DATA);
        }
        if(is_file(GENERATED_FAKE_DATA."_fake.".$type) && $round == 0) {
            //unlink(GENERATED_FAKE_DATA."_fake.".$type);
        }
        //$fp = fopen(GENERATED_FAKE_DATA."_fake.".$type, "a+");

        $content = "";
        $tableInfos = $this->processTableInfos($this->model->getTableInfos($table));
        $tableInfos['table'] = $table;
        //Controller::debug($tableInfos);
        switch ($type) {
            case "sql":
                $this->generateSQLFakeData($tableInfos, $size, $fp, $generateId);
                break;
            case "xml":
                break;
            case "json":
                $this->generateJSONFakeData($tableInfos, $size, $fp, $generateId);
                break;
            default:
                return false;
                break;
        }
        return true;
    }

    private function generateSQLFakeData($infos, $size, $fp, $generateId)
    {
        $content = PHP_EOL . PHP_EOL . "";
        if ($generateId == 'true') {
            if(isset($infos['pk']) && !empty($infos['pk'])) {
                if(!empty($infos['string'])) {
                    $content .= " INSERT INTO " . $infos['table'] . " (" . $infos['pk'] ."," . $infos['string'] . ") " .PHP_EOL;
                } else {
                    $content .= " INSERT INTO " . $infos['table'] . " (" . $infos['pk'] .") ".PHP_EOL;
                }
            } else {
                $content .= " INSERT INTO " . $infos['table'] . " (" . $infos['string'] . ") " .PHP_EOL;
            }
        } else {
            if(empty($infos['string'])) {
                $content .= " INSERT INTO " . $infos['table'] .PHP_EOL;
            } else {
                $content .= " INSERT INTO " . $infos['table'] . " (" . $infos['string'] . ") " .PHP_EOL;
            }
        }
        $content .= " VALUES " ;
        for ($i = 0; $i<$size; $i++) {
            $content .= " (" ;

            foreach ($infos['fields'] as $key => $field) {
                if($field['Name'] == $infos['pk']) {
                    //$content .= $this->guestFakeData($field['Name'], $field['Type'], true);
                    //if($generateId == 'true' || empty($infos['string'])){
                    if($generateId == 'true'){
                        $content .= ($i+1) . ",";
                    }
                } else{
                    $data = $this->guestFakeData( $infos['table'], $field['Name'], $field['Type']);
                    $content .= $data . ", ";
                }
            }
            $content = rtrim($content, ", ");
            $content .= " ),".PHP_EOL;
            $this->generatedData[$infos['table']]["ids"][] = ($i+1);
        }
        $content= rtrim($content, ",".PHP_EOL);
        $content .=";" . PHP_EOL . PHP_EOL;

        fputs($fp,trim($content));
        echo  $content;
        return $content;
    }


    private function generateJSONFakeData($tableInfos, $size, $fp, $generateId)
    {
        $content = "[";

        $content .= "]";
        //echo  "<h1>Under Construction</h1>";
        //fputs($fp,trim($content));
        return $content;
    }

    private function guestFakeData($table,$column, $type, $is_pk = false)
    {
        $data = "";

        if (preg_match("/(^int|long$)/i", $type) ) {
            //echo " * " . $type.PHP_EOL."<br>";
            $data = $this->guestInt($table, $column);
        } elseif (preg_match("/varchar/i", $type)) {
            $length = explode("(",$type);
            $length = $length[1];
            @$length = explode(")",$length);
            $length = $length[0];
            $data = $this->guestString($column, $length, $type, $table);
        } elseif (preg_match("/timestamp/i", $type)) {
            //timestamp
            $data = mktime();
        } elseif (preg_match("/bool|tinyint/i", $type)) {
            $tmp = [1, 0];
            $index = array_rand($tmp);
            $data = $tmp[$index];
        } elseif (preg_match("/(float|double)/i", $type)) {
            $data = $this->float_rand(0, 500);
        } elseif (preg_match("/(text|longtext|blob)/i", $type)) {
            $data = $this->guestString($column, null, $type, $table);
        } elseif (preg_match("/(date$)/i", $type)) {
            $timestamp = mt_rand(1, time());
            $randomDate =  " '" .date("Y-m-d", $timestamp). "' " ;
            $data = $randomDate;
        } elseif (preg_match("/(datetime)/i", $type)) {
            $timestamp = mt_rand(1, time());
            $randomDate =  " '" .date("Y-m-d H:i:s", $timestamp). "' " ;
            $data = $randomDate;
        } elseif (preg_match("/(^time$)/i", $type)) {
            $timestamp = mt_rand(1, time());
            $randomDate =  " '" .date("H:i:s", $timestamp). "' " ;
            $data = $randomDate;
        }
        return $data;
    }

    private function processTableInfos($infos)
    {
        $pk = null;
        $str = "";
        $fields = [];

        foreach ($infos as $key => $value) {
            if($value['Key'] != "PRI") {
                $str .= "" . $value['Name'] . ",";
            } else {
                $pk = $value['Name'];
            }
            $fields[] = [
                "Name" => $value["Name"],
                "Type" => $value["Type"]
            ];
        }
        $str = rtrim($str, ",");

        return ['string' => $str, "pk" => $pk, "fields" => $fields];
    }

    private function sortTables($data)
    {
        $no_related = [];
        $related = [];

        foreach ($data as $key => $value) {
            if(count($this->model->getForeingTables($value)) == 0) {
                $no_related[] = $value;
            } else {
                $related[] = $value;
            }
        }
        return array_merge($no_related, $related);
    }

    function __call($function, $arguments)
    {
        switch ($function)
        {
            case "guestInt":
                $table = $arguments[0];
                $column = $arguments[1];
                $isForeignKey = $this->model->getForeingKeyTable($column, $table);
                if($isForeignKey) {
                    @$rand = array_rand($this->generatedData[$isForeignKey]['ids']);
                    return @$this->generatedData[$isForeignKey]["ids"][$rand];
                    //return " ".$isForeignKey." from ". $table . " and is ". $column;
                }
                return rand(1, 100);
                break;
            case "guestString":
                $column = $arguments[0];
                $size = $arguments[1];
                $type = $arguments[2];
                $table = $arguments[3];

                $isUnique = $this->model->isUnique($column,$table);

                if(preg_match("/(^name|^nom)/i", $column)) {
                    $raw = unserialize(FAKE_NAMES);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " '" . mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(prenom|surname)/i", $column)){
                    $raw = unserialize(FAKE_SURNAMES);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " '" .mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(username|login|pseudo)/i", $column)){
                    $raw = unserialize(FAKE_USERNAMES);
                    if ($isUnique) {
                        if(!isset($this->unique[$table][$column]['SAVED'])) {
                            $this->unique[$table][$column]['SAVED'] = [];
                        }
                        $i = 0;
                        do{
                            $rand = array_rand($raw);
                            //unset($raw[$rand]);
                            $i++;
                        }while(in_array($raw[$rand], $this->unique[$table][$column]['SAVED']) && $i <= count($raw));
                        $this->unique[$table][$column]['SAVED'][] = $raw[$rand];
                    } else {
                        $rand = array_rand($raw);
                    }
                    $data = $raw[$rand];
                    return " '" .mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(label|libelle|title)/i", $column)){
                    $raw = unserialize(FAKE_LABELS);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " '" . mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(pass|password|passe|mot_de)/i", $column)){
                    $size = rand(6, 12);
                    $data = $this->randomPassword($size);
                    return " '" . mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(token)/i", $column)){
                    $data = $this->randomPassword(32);
                    return " '" . mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(email)/i", $column)){
                    $raw = unserialize(FAKE_EMAILS);
                    if ($isUnique) {
                        if(!isset($this->unique[$table][$column]['SAVED'])) {
                            $this->unique[$table][$column]['SAVED'] = [];
                        }
                        $i = 0;
                        do{
                            $rand = array_rand($raw);
                            //unset($raw[$rand]);
                            $i++;
                        }while(in_array($raw[$rand], $this->unique[$table][$column]['SAVED']) && $i <= count($raw));
                        $this->unique[$table][$column]['SAVED'][] = $raw[$rand];
                    } else {
                        $rand = array_rand($raw);
                    }
                    $data = $raw[$rand];
                    return " '" . mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(image|logo)/i", $column)){
                    $raw = unserialize(FAKE_IMAGES);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " '" . mysql_escape_string( $data) . "' ";
                } else if(preg_match("/(text|description|details|body|content|message)/i", $column)){
                    $raw = unserialize(FAKE_DESCRIPTIONS);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " '" . mysql_escape_string( $data) . "' ";
                } else {
                    $tmp = explode("(",$type);
                    $realType = $tmp[0];
                    @$length = explode(")",$tmp[1]);
                    $length = $length[0];

                    if(preg_match("/(varchar|blob|text|desc|message)/i", $realType)) {
                        $raw = unserialize(FAKE_STRINGS);
                        $rand = array_rand($raw);
                        return " '" . mysql_escape_string( $raw[$rand]) . "' ";
                    } elseif (preg_match("/(int)/i", $realType)) {
                        return rand(0,1000);
                    }

                    return "Cannot guest data for field (".$column ." of type $length)";
                }
                break;

        }
        return null;
    }

    private function float_rand($min, $Max, $round=2){
        //validate input
        if ($min > $Max) { $min=$Max; $max=$min; }
        else { $min = $min; $max=$Max; }
        $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        if($round > 0)
            $randomfloat = round($randomfloat,$round);

        return $randomfloat;
    }

    private function randomPassword($size = 8) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $size; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

}