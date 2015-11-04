require(['converse'], function (converse) {
    converse.initialize({
	bosh_service_url: 'https://hub.spaz.org:5281/http-bind/',
	websocket_url: 'wss://hub.spaz.org:5281/websocket/',
	keepalive: true,
	message_carbons: true,
	debug: false, 
	play_sounds: true,
	roster_groups: true,
	show_controlbox_by_default: false,
	xhr_user_search: false
    })});




