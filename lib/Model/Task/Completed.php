<?php
class Model_Task_Completed extends Model_Task_AllMine {
    function init(){
    	parent::init();
    	
    	$this->setMasterField('is_complete',true);
    }
}