<?php

/**
 * Name: Converse XMPP Chat plugin
 * Description: Enables XMPP chat with Converse.js
 * Version: 0.1
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
	$bosh_url = get_config('converse','bosh_url');
	$websockets_url = get_config('converse','websockets_url');
	// TODO: add domain placeholder
	
        if(! $active){
		return;
	}


	// ugly. head_add_js and head_add_css would be so much cleaner, but don't work here for some reason?
	$a->page['htmlhead'] .=  '<link rel="stylesheet" href="' .   
		$a->get_baseurl() . 
		"/addon/converse/converse.min.css" .'" media="all" />';


	$scripts = array("converse.nojquery.min.js");
	
	foreach ($scripts as $js){
		$a->page['htmlhead'] .=   '<script src="' .  
			$a->get_baseurl() . '/addon/converse/'. $js . '"></script>';
	}



	/// ugly, but reliable way to pass in settings to converse.
	// vars documented here https://conversejs.org/docs/html/configuration.html
	$a->page['content'] .= '<script language="javascript" type="text/javascript">' .
		"require(['converse'], function (converse) {
    converse.initialize({
	bosh_service_url: '$bosh_url/',
	websocket_url: '$websockets_url/',
	//domain_placeholder: '', /// TODO add to settings
	keepalive: true,
	animate: false,
	autologin: false, // will be true once jid is populated, WHEN it is populated
	// TODO: provide jid, password, and auto-log them in (pconfig, auto-populate from db)
	message_carbons: true,
	debug: false,  // TODO add to settings (pconfig? config?)
	play_sounds: true, // TODO: let the user decide (pconfig)
	roster_groups: true,
	show_controlbox_by_default: false, //TODO: add to pconfig
	xhr_user_search: false
    })});" .
		';</script>';
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
							   is_site_admin() ? "Enable/disable for the ADMIN user (does not affect other users)" : ""), 
							 $checked, 
							 '', 
							 array(t('No'),
							       t('Yes')))));


	if( is_site_admin() ){
		$bosh_url = get_config('converse','bosh_url');
		$sc .= replace_macros(get_markup_template('field_input.tpl'), 
				      array('$field' => array('bosh_url', 
							      t('Path to BOSH host.'), 
							      $bosh_url, 
							      t('Full URL, with http:// or https://, and /http-bind at the end'))));
		$websockets_url = get_config('converse','websockets_url');
		$sc .= replace_macros(get_markup_template('field_input.tpl'), 
				      array('$field' => array('websockets_url', 
							      t('Path to websockets host.'), 
							      $websockets_url, 
							      t('Full URL, with ws:// or wsss://, and websocket or whatever at the end'))));
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
		set_config('converse','bosh_url',trim($_POST['bosh_url']));
		set_config('converse','websockets_url',trim($_POST['websockets_url']));
		info( t('Converse Settings updated.') . EOL);
	}

}


