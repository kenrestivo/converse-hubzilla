<?php

/**
 * Name: Converse XMPP Chat plugin
 * Description: Enables XMPP chat with Converse.js
 * Version: 0.2
 * Author: ken restivo <ken@restivo.org>
 */

require_once('XMPP-BOSH-toolkit/lib/XmppBosh.php');


function converse_load(){
	register_hook('construct_page', 'addon/converse/converse.php', 'converse_all_pages');

	register_hook('feature_settings', 'addon/converse/converse.php', 'converse_settings');
	register_hook('feature_settings_post', 'addon/converse/converse.php', 'converse_settings_post');

}


function converse_unload(){
	unregister_hook('construct_page', 'addon/converse/converse.php', 'converse_content');
}


function converse_all_pages(&$a, &$b){
	// chat's really only for us
	if(! local_channel())
		return;

	$active = get_pconfig(local_channel(), 'converse', 'enable');
	$bosh_url = get_config('converse','bosh_url');
	
        if(! $active){
		return;
	}

	// ugly. head_add_js and head_add_css would be so much cleaner, but don't work here for some reason?
	$a->page['htmlhead'] .=  '<link rel="stylesheet" href="' .   
		$a->get_baseurl() . 
		"/addon/converse/converse.min.css" .'" media="all" />';

	
	$a->page['htmlhead'] .=   '<script src="' .  
		$a->get_baseurl() . '/addon/converse/'. "converse.nojquery.min.js" . '"></script>';


	// vars documented here https://conversejs.org/docs/html/configuration.html
	$a->page['content'] .= '<script language="javascript" type="text/javascript">' .
		"require(['converse'], function (converse) {
	$.ajax({
		type:'GET',
		url: '" . $a->get_baseurl() . "/converse/config'," .
		"dataType: 'json',
		success: function(data){ 
                  converse.initialize(data);
                 }})});" .
		'</script>';
	// NOTE: there's no additional content necessary, the JS above loads everything needed.

}




function converse_settings(&$a,&$s) {

	// Shouldn't these only show up if the plugin is enabled?

	if(! local_channel())
		return;



	$username = get_pconfig(local_channel(),'converse','username');
	$password = get_pconfig(local_channel(),'converse','password');

	$enabled = get_pconfig(local_channel(),'converse','enable');
   
	$checked = (($enabled) ? 1 : false);

	if(is_site_admin()){
		$msg = "Enable/disable for the ADMIN user (does not affect other users)";
	}
	
	$sc .= replace_macros(get_markup_template('field_checkbox.tpl'), array(
				      '$field'	=> array('enable', 
							 t('Enable Converse.js XMPP Chat Plugin' . $msg),
							 $checked, 
							 '', 
							 array(t('No'),
							       t('Yes')))));

	$sc .= replace_macros(get_markup_template('field_input.tpl'), 
			      array('$field' => array('username', 
						      t('Username (with no @)'), 
						      $username, 
						      t('Jabber username'))));

	$sc .= replace_macros(get_markup_template('field_password.tpl'), 
			      array('$field' => array('password', 
						      t('Password (stored in plaintext for now)'), 
						      $password, 
						      t('Jabber password'))));



	if( is_site_admin() ){
		$domain = get_config('converse','domain');
		$bosh_url = get_config('converse','bosh_url');
		$sc .= replace_macros(get_markup_template('field_input.tpl'), 
				      array('$field' => array('bosh_url', 
							      t('URL to BOSH host.'), 
							      $bosh_url, 
							      t('Full URL, with http:// or https://, and /http-bind at the end'))));

		$sc .= replace_macros(get_markup_template('field_input.tpl'), 
				      array('$field' => array('domain', 
							      t('XMPP Domain.'), 
							      $domain, 
							      t('The domain of your XMPP server.'))));

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

	set_pconfig(local_channel(),'converse','enable',intval($_POST['enable']));
	set_pconfig(local_channel(),'converse','username',$_POST['username']);
	set_pconfig(local_channel(),'converse','password',$_POST['password']);
	
	if(is_site_admin() && $_POST['converse-submit']) {
		set_config('converse','domain',trim($_POST['domain']));
		set_config('converse','bosh_url',trim($_POST['bosh_url']));
		info( t('Converse Settings updated.') . EOL);
	}

}


function converse_content(&$a){
	// TODO: shadow the admin settings here
	return "Converse XMPP chat module";
}



/*
  It looks like the CLIENT caches the sid and rid, so there's no need to save them
  in the database on the server. It'll hit the prebind url when it needs to refresh.
 */
function prebind(){

	$username = get_pconfig(local_channel(),'converse','username');
	$password = get_pconfig(local_channel(),'converse','password');


	$domain = get_config('converse','domain');
	$bosh_url = get_config('converse','bosh_url');

	$xmppBosh = new XmppBosh($domain, $bosh_url, 
				 'converse-hubzilla', // TODO: must have unique per session?
				 ((strpos($bosh_url, 'https://') > 0) ? true : false));
	$xmppBosh->connect($username, $password);


	return $xmppBosh->getSessionInfo();

}



function converse_init(&$a) {
        if(! local_channel())
		return;
	
	$x = argc(); 
	if($x > 1){
		switch(argv(1)){
		case "prebind":
			json_return_and_die(prebind());
			break;
		case "config":
			$bosh_url = get_config('converse','bosh_url');
			$domain = get_config('converse','domain');
			$username = get_pconfig(local_channel(), 'converse', 'username');
			json_return_and_die(
				array("bosh_service_url" => $bosh_url,
				      "keepalive" => true,
				      "prebind_url" =>  $a->get_baseurl() . '/converse/prebind',
				      "animate" => false,
				      'jid' => $username . '@' . $domain, /// XXX resource needed TODO
				      "authentication" => 'prebind',
				      'allow_registration' => false,
				      "autologin" => true,
				      "message_carbons" => true,
				      "debug" => false,  // TODO add to settings (pconfig? config?)
				      "play_sounds" => true, // TODO: let the user decide (pconfig)
				      "roster_groups" => true,
				      "show_controlbox_by_default" => false, //TODO: add to pconfig
				      "xhr_user_search" => false));
			break;
		}
	}
}

function converse_module() { return; }

