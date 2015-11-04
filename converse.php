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
	// chat's really only for us
	if(! local_channel())
		return;

	$active = get_pconfig(local_channel(), 'converse', 'enable');
	$bosh_path = get_config('converse','bosh_path');
	$websockets_path = get_config('converse','websockets_path');

	
        if(! $active){
		return;
	}


	// ugly. head_add_js and head_add_css would be so much cleaner, but don't work here for some reason?
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



function converse_settings(&$a,&$s) {

	if(! local_channel())
		return;




	$enabled = get_pconfig(local_channel(),'converse','enable');
   
	$checked = (($enabled) ? 1 : false);


	$sc .= replace_macros(get_markup_template('field_checkbox.tpl'), array(
				      '$field'	=> array('converse', 
							 t('Enable Converse.js XMPP Chat Plugin' .
							   is_site_admin() ? "For the ADMIN user (does not affect other users)" : ""), 
							 $checked, 
							 '', 
							 array(t('No'),
							       t('Yes')))));


	if( is_site_admin() ){
		$bosh_path = get_config('converse','bosh_path');
		$sc .= replace_macros(get_markup_template('field_input.tpl'), 
				      array('$field' => array('bosh_path', 
							      t('Path to BOSH host.'), 
							      $bosh_path, 
							      t('Full path, with http:// or https://, and /http-bind at the end'))));
		$websockets_path = get_config('converse','websockets_path');
		$sc .= replace_macros(get_markup_template('field_input.tpl'), 
				      array('$field' => array('websockets_path', 
							      t('Path to websockets host.'), 
							      $websockets_path, 
							      t('Full path, with ws:// or wsss://, and websocket or whatever at the end'))));
	}
				      

	$s .= replace_macros(get_markup_template('generic_addon_settings.tpl'), 
			     array('$addon' => array('converse',
						     t('Converse Settings'),
						     '', 
						     t('Submit')),
				   '$content' => $sc));

}


function converse_settings_post($a,&$post) {
        if(! local_channel())
		return;

	set_pconfig(local_channel(),'converse','enable',intval($_POST['converse']));
	
	if(is_site_admin() && $_POST['converse-submit']) {
		set_config('converse','bosh_path',trim($_POST['bosh_path']));
		set_config('converse','websockets_path',trim($_POST['websockets_path']));
		info( t('Converse Settings updated.') . EOL);
	}

}


