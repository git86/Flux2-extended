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
    
    	$left->add('TaskBox')->add('Controller_TaskCompletion');
    	    	    
    
    
    	$crud=$right->add('MyGrid');
    	
    	$crud->setModel('Task_Completed');
    	
		$this->api->template->set('footer_text','123');  
		
		if($right->add('Button')->isClicked()){
			$this->js()->univ()->dialogOK('Hello','World',
				$this->js()->_enclose()->univ()->alert(123)
			
			)->execute();
		}
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

	public $grid;

	function init(){
		parent::init();
		
		$columns=$this->add('Columns');
		
		$form=$columns->addColumn()->add('NewTaskForm');
		$form->setFormClass('vertical');
    	$field = $form->addField('line','new_task','New Task:');
    	//$button = $form->addSubmit();
    	
    	
    	$search=$columns->addColumn()->add('Form');
    	$search_field=$search->addField('search','q','Search Tasks:');
		
		$search_field->js('change',$search->js()->submit());
		$search_field->js(true)->univ()->autoChange(300);
		
		$tasks=$this->add('Model_Task_Mine')->getRows();
		$task_names=array();
		foreach($tasks as $task){
			$task_names[]=$task['name'];
		}
		
		$search_field->js(true)->autocomplete(array('source'=>$task_names));
		
		$search->setFormClass('vertical');

		$search_panel=$this->add('View');
    	$grid=$search_panel->add('MVCGrid');
    	
    	if($search->isSubmitted()){
    		$q=$search->get('q');
 			$search_panel->js()->reload(array('q'=>$q))->execute();
    	}
    	
    	
    	$field -> js('click')->select();
    	
    	$this->grid=$grid;
    	
    	$grid->setModel('Task_Mine',array('name'));
    	
    	$this->api->stickyGET('q');
    	
    	$grid->dq->where('name like','%'.$_GET['q'].'%');
    	$grid->dq->order('id',true);
    	
    	$q = clone $grid->dq;
    	$q->field('count(*)');
    	
    	
    	$js=array();
    	$js[] = $search_panel->js()->reload();

    	$count=$q->do_getOne();
		if($count==0  && $_GET['q']){
			$search_panel->add('View_Info')->set('Item is not found. Would you like to setup a reminder?');
			$form_reminder = $search_panel->add('NewTaskForm');
			$form_reminder->addField('line','new_task')->set($_GET['q']);
			$form_reminder->addSubmit('Yes');
			$form_reminder->addButton('No')->setLabel('No, Thanks')
				->js('click',$form_reminder->js()->hide('slow'));
			
			$form_reminder->onAddition($js);
			
			
			$grid->destroy();
		}    	
    	$grid->addPaginator(10);
    	
       	$js[] = $search->js()->find('form')->removeClass('form_changed');
    	$js[] = $search->js()->reload();
   	
    	
    	
    	$form->onAddition($js);	
    	
    	
    	/*$form=$this->add('Form');
    	$form->addField('autocomplete','task')->setModel('Task_Mine');
		if($form->isSubmitted()){
			$this->js()->univ()->alert($form->get('task'))->execute();
		}
		*/

	}
}


class Controller_TaskCompletion extends AbstractController {
	function init(){
		parent::init();
	
		
		$form=$this->owner->add('Form');
		$selection = $form->addField('line','selection');
		$this->owner->grid->addSelectable($selection);
		
		$this->owner->add('Button')->setLabel('Complete')
			->js('click',$form->js()->submit());
			
		$form->js(true)->hide();
		
		$model= $this->owner->grid->getController()->getModel();
		
		if($form->isSubmitted()){
			$ids=json_decode($form->get('selection'));
			
			if(!$ids){
				$this->owner->js()->univ()->dialogOK('Erorr','Select a task first')->execute();
			}
			
			$model->completeMultipleTasks($ids);
			
			$this->owner->js(null,array(
				$this->api->page_object->js()->find('.form_changed')->removeClass('form_changed'),
				$this->api->page_object->js()->reload()
				)
			)->univ()->successMessage('Tasks Completed!')->execute();
		}
	}
}

class NewTaskForm extends Form {
	function onAddition($js){
		if($this->isSubmitted()){
			$form=$this;
		
			
    		$task = $this->add('Model_Task_Mine');
    		$task->set('name',$form->get('new_task'));
    		$task->set('due_date',date('Y-m-d',strtotime('tomorrow')));
    		$task->update();
    		
		    $form->js(null, $js)->univ()->successMessage('task was added successfully')->execute();

		}
	}
}