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
	head_add_js($a->get_baseurl() . "/addon/converse/converse.min.js");
	head_add_css($a->get_baseurl() . "/addon/converse/converse.min.css");
}


function convese_module() {
	return;
}