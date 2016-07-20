<?php
    class T1Controller {
        private $id_t1;
        private $lib;
    
         
		public function __construct($data = ''){
            //todo stuff here :) 
            	parent::__construct($data);
        }
        
		public function index(){
    		$this->display();
		}

		public function add(){
        	if($_SERVER['REQUEST_METHOD'] == 'GET'){
            	//Display form here
            	$this->view->render('t1/add');
        	}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            	//Proccess Form here
            	$obj = new T1Controller($_POST);
             
	//	var_dump($obj);
            	$action = $this->model->add($obj);

            
	echo $action['message'];
            
	echo "<a href=".URL."T1>Back</a>";
            
        	}
		}

		public function edit($id=null){
    
        	if($_SERVER['REQUEST_METHOD'] == 'GET'){
            	//Display form here
            		$data = $this->model->getInfos($id);
                		$this->view->data = $data;
                		$this->view->render('t1/edit'); 
        	}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            	//Proccess Form here            
            //var_dump($_POST);
            	$obj = new T1Controller($_POST);
             
	//	var_dump($obj);
            	$action = $this->model->edit($obj);
            
            
				if($action['status']) {
            				echo $action['message'];
            				echo "<a href=".URL."T1>Back</a>";
            
				}
        	}        
		}


		public function delete($id){
    //		$this->view->Id = $id;
    //	$obj = new T1Controller($_GET);
    			$action = $this->model->delete($id);
    		$this->view->message = $action;
    		$this->view->render('t1/delete');
		}

		public function display(){
    		$this->view->getData = $this->model->listAll();
    		$this->view->render('t1/display');
		}
                
        
        
		public function getId_t1(){
			return $this->id_t1;
		}
		public function getLib(){
			return $this->lib;
		}
        
		public function setId_t1($arg){
			$this->id_t1 = $arg;
		}
		public function setLib($arg){
			$this->lib = $arg;
		}
        
    }