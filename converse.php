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


	$scripts = array("salsa20.js",
			 "bigint.js",
			 "core.js",
			 "enc-base64.js",
			 "md5.js",
			 "evpkdf.js",
			 "cipher-core.js",
			 "aes.js",
			 "sha1.js",
			 "sha256.js",
			 "hmac.js",
			 "pad-nopadding.js",
			 "mode-ctr.js",
			 "eventemitter.js",
			 "otr.js",
			 "strophe.js",
			 "strophe.vcard.js",
			 "strophe.disco.js",
			 "strophe.ping.js",
			 "underscore.js",
			 "backbone.js",
			 "backbone.browserStorage.js",
			 "backbone.overview.js",
			 "moment-with-locales.js",
			 "jquery.browser.js",
			 "index.js",
			 "jed.js",
			 "locales.js",
			 "templates.js",
			 "utils.js",
			 "converse.js");
	
	foreach ($scripts as $js){
		$a->page['htmlhead'] .=   '<script src="' .  
			$a->get_baseurl() . '/addon/converse/'. $js . '"></script>';
			
			
	}
}


function convese_module() {
	return;
}