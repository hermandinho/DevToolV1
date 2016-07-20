<?php
    class Controller
    {
        protected $model;

        function __construct() {
            $this->view = new View();
            $this->model = new Model();
        }
        
        public function loadModel($name){
            $path = "models/".ucfirst($name)."Model.php";
            if(file_exists($path)){
                require_once $path;
                $modelName = ucfirst($name)."Model";
                $this->model = new $modelName();
            }
        }

        public static function debug($data)
        {
            echo "<pre>";
                print_r($data);
            echo "</pre><hr>";
        }
    }