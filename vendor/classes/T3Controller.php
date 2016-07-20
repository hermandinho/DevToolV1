<?php
    class T3Controller {
        private $id;
        private $otherTab;
        private $field;
        private $ID_t2;
        private $Id_T1;
        private $is_bool;
    
         
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
            	$this->view->render('t3/add');
        	}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            	//Proccess Form here
            	$obj = new T3Controller($_POST);
             
	//	var_dump($obj);
            	$action = $this->model->add($obj);

            
	echo $action['message'];
            
	echo "<a href=".URL."T3>Back</a>";
            
        	}
		}

		public function edit($id=null){
    
        	if($_SERVER['REQUEST_METHOD'] == 'GET'){
            	//Display form here
            		$data = $this->model->getInfos($id);
                		$this->view->data = $data;
                		$this->view->render('t3/edit'); 
        	}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            	//Proccess Form here            
            //var_dump($_POST);
            	$obj = new T3Controller($_POST);
             
	//	var_dump($obj);
            	$action = $this->model->edit($obj);
            
            
				if($action['status']) {
            				echo $action['message'];
            				echo "<a href=".URL."T3>Back</a>";
            
				}
        	}        
		}


		public function delete($id){
    //		$this->view->Id = $id;
    //	$obj = new T3Controller($_GET);
    			$action = $this->model->delete($id);
    		$this->view->message = $action;
    		$this->view->render('t3/delete');
		}

		public function display(){
    		$this->view->getData = $this->model->listAll();
    		$this->view->render('t3/display');
		}
                
        
        
		public function getId(){
			return $this->id;
		}
		public function getOtherTab(){
			return $this->otherTab;
		}
		public function getField(){
			return $this->field;
		}
		public function getID_t2(){
			return $this->ID_t2;
		}
		public function getId_T1(){
			return $this->Id_T1;
		}
		public function getIs_bool(){
			return $this->is_bool;
		}
        
		public function setId($arg){
			$this->id = $arg;
		}
		public function setOtherTab($arg){
			$this->otherTab = $arg;
		}
		public function setField($arg){
			$this->field = $arg;
		}
		public function setID_t2($arg){
			$this->ID_t2 = $arg;
		}
		public function setId_T1($arg){
			$this->Id_T1 = $arg;
		}
		public function setIs_bool($arg){
			$this->is_bool = $arg;
		}
        
    }