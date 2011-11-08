<?php

class page_account extends Page {
    function init(){
        parent::init();
        
        $this->api->auth->check();
            
    	$this->add('FormAndSave')->setModel('User_Me');
    
    }
}
        