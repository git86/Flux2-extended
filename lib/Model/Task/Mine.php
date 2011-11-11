<?php
class Model_Task_Mine extends Model_Task_AllMine {
    function init(){
    	parent::init();
    	
    	$this->setMasterField('is_complete',false);
    }
    function completeMultipleTasks($ids){
    	$q=$this->dsql();
    	$q->set('is_complete','Y');
    	$q->where('id in',$ids);
    	$q->do_update();
    	
    }
}