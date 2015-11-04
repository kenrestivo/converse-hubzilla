<?php

/**
 * Name: Converse XMPP Chat plugin
 * Description: Enables XMPP chat with Converse.js
 * Version: 1.0
 * Author: ken restivo <ken@restivo.org>
 */




function converse_load(){
	register_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
	register_hook('feature_settings', 'addon/converse/converse.php', 'converse_settings');
	register_hook('feature_settings_post', 'addon/converse/converse.php', 'converse_settings_post');

}


function converse_unload(){
	unregister_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_content(&$a, &$b){

	/* 
         * $active = get_pconfig(local_channel(), 'converse', 'enable');
	 * 
         * if(! $active){
	 * 	return;
	 * }
         */


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
	// NOTE: there's no additional content necessary, the JS above loads everything needed.
}



function converse_addon_settings(&$a,&$s) {
	if(! local_channel())
		return;

//	$bosh = get_config('converse','bosh_path');
//	$websockets = get_config('converse','websockets_path');

	$enabled = get_pconfig(local_channel(),'converse','enable');
   
	$checked = (($enabled) ? 1 : false);


	$sc .= replace_macros(get_markup_template('field_checkbox.tpl'), array(
				      '$field'	=> array('converse', 
							 t('Enable Converse.js XMPP Chat Plugin'), 
							 $checked, 
							 '', 
							 array(t('No'),
							       t('Yes')))));
				      

	$s .= replace_macros(get_markup_template('generic_addon_settings.tpl'), array(
				     '$addon' 	=> array('converse',
							 t('Converse Settings'),
							 '', 
							 t('Submit')),
				     '$content'	=> $sc));

}


function converse_settings_post($a,&$req) {
        if(! local_channel()){
		return;
	}

	if($_POST['converse-submit']) {
		set_pconfig(local_channel(),'converse','enable',intval($_POST['converse']));
		info( t('Converse Settings updated.') . EOL);
	}



}


