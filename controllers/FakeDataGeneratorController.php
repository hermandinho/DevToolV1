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
        $this->view->DBFields = $data;
        $this->view->render("fake/index");
    }

    private function proccessMetaData($data = [])
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

        Controller::debug($no_related);
        echo "*****************************************************************************";
        Controller::debug($related);
    }
}