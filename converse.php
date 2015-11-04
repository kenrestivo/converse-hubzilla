<?php


function converse_load(){
	register_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_unload(){
	unregister_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_content(&$a){
    $a->page['htmlhead'] .= 
  $a->get_baseurl()
}


function convese_module() {
	return;
}