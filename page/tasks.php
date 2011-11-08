<?php

class page_tasks extends Page {
    function init(){
        parent::init();
        
        $this->api->auth->check();
        
                   
        $user=$this->api->auth->get('name');
        
        $this->add('Text')->set('Welcome, '.$user);
    
    
    	$col=$this->add('Columns');
    	
    	$left=$col->addColumn();
    	$right=$col->addColumn();
    
    	$left->add('TaskBox');
    	    	    
    
    
    	$crud=$right->add('MyCRUD');
    	
    	$crud->setModel('Task_Mine');
    	
		$this->api->template->set('footer_text','123');  
    }
}

class MyCRUD extends CRUD {
	public $grid_class='MyGrid';
}
class MyGrid extends MVCGrid {

	function formatRow(){
		$date=strtotime($this->current_row['due_date']);
	

		if($date && $date<time()){
			
			/*
			$this->current_row['due_date']=
				'<span style="color: red">'.
				$this->current_row['due_date'].
				'</span>';	
			*/
			$this->setTDParam('due_date','style','color: red');
		}else{
			$this->setTDParam('due_date','style','');
		
		}
		parent::formatRow();

	}

}
        
        
class TaskBox extends View {
	function init(){
		parent::init();
		
		$form=$this->add('MVCForm');
    	$field = $form->addField('line','new_task');
    	$button = $form->addSubmit();
    	
    	
    	$field -> js('click')->select();
    	
    	
    	$grid=$this->add('MVCGrid');
    	$grid->setModel('Task_Mine',array('name'));

    	if($form->isSubmitted()){
    	
    		$task = $this->add('Model_Task_Mine');
    		$task->set('name',$form->get('new_task'));
    		$task->set('due_date',date('Y-m-d',strtotime('tomorrow')));
    		$task->update();
    		
    		$js = $grid->js()->reload();
    		
    		$form->js(null, $js)->univ()->successMessage('task was added successfully')->execute();
    	}

	}

}