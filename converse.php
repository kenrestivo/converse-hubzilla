<?php

/**
 * Name: Converse XMPP Chat plugin
 * Description: Enables XMPP chat with Converse.js
 * Version: 1.0
 * Author: ken restivo <ken@restivo.org>
 */




function converse_load(){
	register_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_unload(){
	unregister_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_content(&$a, &$b){


//converse.css themes.css
	// head_add_js and head_add_css would be so much cleaner, but don't work here for some reason.
	$a->page['htmlhead'] .=  '<link rel="stylesheet" href="' .   
		$a->get_baseurl() . 
		"/addon/converse/converse.min.css" .'" media="all" />';


	$scripts = array("converse.nojquery.min.js",
			 "chat.js");
	
	foreach ($scripts as $js){
		$a->page['htmlhead'] .=   '<script src="' .  
			$a->get_baseurl() . '/addon/converse/'. $js . '"></script>';
			
			
	}
}


function convese_module() {
	return;
}