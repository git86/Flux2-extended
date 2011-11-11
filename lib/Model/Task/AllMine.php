<?php
class Model_Task_AllMine extends Model_Task {
    function init(){
    	parent::init();
    	
    	$this->setMasterField('user_id',$this->api->auth->get('id'));
    }
}