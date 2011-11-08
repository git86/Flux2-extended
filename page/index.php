<?php

class page_index extends Page {
    function init(){
        parent::init();

		if($this->api->auth->isLoggedIn())
			$this->api->redirect('tasks');

        $crud=$this->add('CRUD');
        
        $model=$crud->setModel('User',array('email','name'));
	    
	    
	    if($crud->grid){

	    }


    }
}
