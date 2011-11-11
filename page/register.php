<?php

class page_register extends Page {
    function init(){
        parent::init();
       
        $form=$this->add('MVCForm'); 
    	$form->setModel('User');
    	
    	$password = $form->getElement('password');
    	$password->add('StrengthChecker',null,'before_field');
    	
    	
    	$button = $form->addSubmit('Register');    	
    	
    	if($form->isSubmitted()){
    	
    		$form->update();
    		
    		$form->js()->hide("slow")->execute();
    	}
    }
    
}