<?php

/**
 * Created by PhpStorm.
 * User: Herman
 * Date: 20/07/2016
 * Time: 13:32
 */
class FakeDataGeneratorController extends Controller
{

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
        $table = $_POST['table'];
        $type = $_POST['type'];
        $number = $_POST['number'];
        $generateId = $_POST['generateId'];

        $this->proccessMetaData($table, $type, $number, $generateId);
    }

    private function proccessMetaData($table, $type, $number, $generateId)
    {
        $desc = $this->model->getForeingTables($table);

        if (count($desc) == 0) {
            $this->generateSimple($table, $type, $number, $generateId);
        } else {
            echo "Complex";
        }
    }

    private function generateSimple($table, $type, $number, $generateId)
    {
        //echo "Generate " . $number ." data of type ".$type . " for ".$table ."<br>";
        $this->generateFakeData($table, $number, $type, $generateId);
    }

    private function generateComplex($table, $type, $number)
    {}

    private function generateFakeData($table, $size, $type, $generateId)
    {
        if(!is_dir(GENERATED_FAKE_DATA))
        {
            mkdir(GENERATED_FAKE_DATA);
        }
        if(is_file(GENERATED_FAKE_DATA."_fake.".$type)) {
            //unlink(GENERATED_FAKE_DATA."_fake.".$type);
        }
        $fp = fopen(GENERATED_FAKE_DATA."_fake.".$type, "a+");

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
                break;
            default:
                return false;
                break;
        }
        return true;
    }

    private function generateSQLFakeData($infos, $size, $fp, $generateId)
    {
        $content = "";
        if ($generateId == 'true') {
            $content .= " INSERT INTO " . $infos['table'] . " (`" . $infos['pk'] ."`," . $infos['string'] . ") " .PHP_EOL;
        } else {
            $content .= " INSERT INTO " . $infos['table'] . " (" . $infos['string'] . ") " .PHP_EOL;
        }

        for ($i = 0; $i<$size; $i++) {
            $content .= " VALUES (" ;

            foreach ($infos['fields'] as $key => $field) {
                if($field['Name'] == $infos['pk']) {
                    //$content .= $this->guestFakeData($field['Name'], $field['Type'], true);
                    if($generateId == 'true'){
                        $content .= ($i+1) . ",";
                    }
                } else{
                    $data = $this->guestFakeData($field['Name'], $field['Type']);
                    $content .= $data . ", ";
                }
            }
            $content = rtrim($content, ", ");
            $content .= " ),".PHP_EOL;
        }
        $content= rtrim($content, ",".PHP_EOL);
        $content .=";" . PHP_EOL . PHP_EOL;

        fputs($fp,trim($content));
        echo  $content;
        return $content;
    }

    private function guestFakeData($column, $type, $is_pk = false)
    {
        $data = "";

        if (preg_match("/(int|long)/i", $type) ) {
            //echo " * " . $type.PHP_EOL."<br>";
            $data = $this->guestInt();
        } elseif (preg_match("/varchar/i", $type)) {
            $length = explode("(",$type);
            $length = $length[1];
            $length = explode(")",$length);
            $length = $length[0];
            $data = $this->guestString($column, $length);
        } elseif (preg_match("/timestamp/i", $type)) {
            //timestamp
            $data = mktime();
        } elseif (preg_match("/bool/i", $type)) {
            $tmp = [true, false];
            $index = array_rand($tmp);
            $data = $tmp[$index];
        } elseif (preg_match("/(float|double)/i", $type)) {
            $data = $this->float_rand(0, 500);
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
                $str .= "'" . $value['Name'] . "',";
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
                return rand(1, 100);
                break;
            case "guestString":
                $column = $arguments[0];
                $size = $arguments[1];

                if(preg_match("/(name|nom)/i", $column)) {
                    $raw = unserialize(FAKE_NAMES);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " '" . $data . "' ";
                } else if(preg_match("/(prenom|surname)/i", $column)){
                    $raw = unserialize(FAKE_SURNAMES);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " `" . $data . "` ";
                } else if(preg_match("/(label|libelle)/i", $column)){
                    $raw = unserialize(FAKE_LABELS);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " `" . $data . "` ";
                } else if(preg_match("/(image|logo)/i", $column)){
                    $raw = unserialize(FAKE_IMAGES);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " `" . $data . "` ";
                } else if(preg_match("/(text|description|details|body|content)/i", $column)){
                    $raw = unserialize(FAKE_DESCRIPTIONS);
                    $rand = array_rand($raw);
                    $data = $raw[$rand];
                    return " `" . $data . "` ";
                } else {
                    return "Cannot guest data for field (".$column .")";
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
}