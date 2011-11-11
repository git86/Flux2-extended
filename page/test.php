<?php
class page_test extends Page_SchemaGenerator {

	function init(){
		parent::init();
		
		/*
		$acc=$this->add('View_Accordion');
		$acc->addSection('one')->add('HelloWorld');
		$acc->addSection('two')->add('LoremIpsum');
		*/
		$p=$this;
		
$this->api->addLocation(
    'atk4-addons/misc/templates/js','js')
    ->setParent($this->api->pathfinder->base_location);
		$p->js()->_load('univ.google.map');

		$map=$p->add('View_Google_Map');
		$map->renderMap(33.35,-6.26);
		$map->width=390; $map->height=300;	


		$form=$this->add('Form');
		$lat=$form->addField('line','lat');
		$long=$form->addField('line','long');
		$zoom=$form->addField('line','zoom');
	
		$form->js(true)->hide();
		
		$this->add('Button')->js('click',$form->js()->submit());
	
		$map->bindLatLngZoom($lat,$long,$zoom);	
	$map->showMapForEdit();

	}
}