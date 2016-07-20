<?php
    class T2Controller {
        private $Id_t2;
        private $lib2;
        private $lib3;
        private $Id_T3;
    
         
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
            	$this->view->render('t2/add');
        	}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            	//Proccess Form here
            	$obj = new T2Controller($_POST);
             
	//	var_dump($obj);
            	$action = $this->model->add($obj);

            
	echo $action['message'];
            
	echo "<a href=".URL."T2>Back</a>";
            
        	}
		}

		public function edit($id=null){
    
        	if($_SERVER['REQUEST_METHOD'] == 'GET'){
            	//Display form here
            		$data = $this->model->getInfos($id);
                		$this->view->data = $data;
                		$this->view->render('t2/edit'); 
        	}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
            	//Proccess Form here            
            //var_dump($_POST);
            	$obj = new T2Controller($_POST);
             
	//	var_dump($obj);
            	$action = $this->model->edit($obj);
            
            
				if($action['status']) {
            				echo $action['message'];
            				echo "<a href=".URL."T2>Back</a>";
            
				}
        	}        
		}


		public function delete($id){
    //		$this->view->Id = $id;
    //	$obj = new T2Controller($_GET);
    			$action = $this->model->delete($id);
    		$this->view->message = $action;
    		$this->view->render('t2/delete');
		}

		public function display(){
    		$this->view->getData = $this->model->listAll();
    		$this->view->render('t2/display');
		}
                
        
        
		public function getId_t2(){
			return $this->Id_t2;
		}
		public function getLib2(){
			return $this->lib2;
		}
		public function getLib3(){
			return $this->lib3;
		}
		public function getId_T3(){
			return $this->Id_T3;
		}
        
		public function setId_t2($arg){
			$this->Id_t2 = $arg;
		}
		public function setLib2($arg){
			$this->lib2 = $arg;
		}
		public function setLib3($arg){
			$this->lib3 = $arg;
		}
		public function setId_T3($arg){
			$this->Id_T3 = $arg;
		}
        
    }