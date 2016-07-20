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

        $this->proccessMetaData($table, $type, $number);
    }

    private function proccessMetaData($table, $type, $number)
    {
        $desc = $this->model->getForeingTables($table);

        if (count($desc) == 0) {
            $this->generateSimple($table, $type, $number);
        } else {
            echo "COmplex";
        }
    }

    private function generateSimple($table, $type, $number)
    {
        //echo "Generate " . $number ." data of type ".$type . " for ".$table ."<br>";
        $this->generateFakeData($table, $number, $type);
    }

    private function generateComplex($table, $type, $number)
    {}

    private function generateFakeData($table, $size, $type)
    {
        if(!is_dir(GENERATED_FAKE_DATA))
        {
            mkdir(GENERATED_FAKE_DATA);
        }
        $fp = fopen(GENERATED_FAKE_DATA."_fake.".$type, "w");

        $content = "";
        $tableInfos = $this->processTableInfos($this->model->getTableInfos($table));
        $tableInfos['table'] = $table;
        //Controller::debug($tableInfos);

        switch ($type) {
            case "sql":
                $this->generateSQLFakeData($tableInfos, $size, $fp);
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

    private function generateSQLFakeData($infos, $size, $fp)
    {
        $content = "";
        $content .= " INSERT INTO " . $infos['table'] . " ('" . $infos['pk'] ."'," . $infos['string'] . ") " .PHP_EOL;

        for ($i = 0; $i<$size; $i++) {
            $content .= " VALUES (" ;

            foreach ($infos['fields'] as $key => $field) {
                if($field['Name'] == $infos['pk']) {
                    //$content .= $this->guestFakeData($field['Name'], $field['Type'], true);
                    $content .= ($i+1) . ",";
                } else{
                    $data = $this->guestFakeData($field['Name'], $field['Type']);
                    $content .= $data;

                }

            }
            $content .= " ),".PHP_EOL;
        }

        echo $content;
    }

    private function guestFakeData($column, $type, $is_pk = false)
    {
        $data = "";
        //echo " * " . $type.PHP_EOL;
        if (preg_match("/int/i", $type) ) {
            $data = $this->guestInt() . ", ";
        } elseif (preg_match("/varchar/i", $type)) {
            $length = explode("(",$type);
            $length = $length[1];
            $length = explode(")",$length);
            $length = $length[0];

            $data = $this->guestString($column, $length) . ", ";
        } elseif (preg_match("/timestamp/i", $type)) {
            //timestamp
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

    function __call($name, $arguments)
    {
        switch (true)
        {
            case "gustInt":
                return rand(1, 100);
                break;
            case "guestString":

                //if(preg_match("/[name]/i"))

                break;
        }
        return null;
    }


}