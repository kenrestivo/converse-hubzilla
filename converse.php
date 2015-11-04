<?php


function converse_load(){
	register_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_unload(){
	unregister_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_content(&$a){
	$a->page['htmlhead'] .=  '<link rel="stylesheet" href="' .   
		$a->get_baseurl() . 
		"/addon/converse/converse.min.js" .'" media="all" />';
    
	$a->page['htmlhead'] .=   '<script src="' . 
		$a->get_baseurl() . '/addon/converse/converse.min.js' .
		'"></script>';
}


function convese_module() {
	return;
}