require(['converse'], function (converse) {
    converse.initialize({
	bosh_service_url: 'https://hub.spaz.org:5281/http-bind/',
	websocket_url: 'wss://hub.spaz.org:5281/websocket/',
	keepalive: true,
	message_carbons: true,
	debug: true, /// not for production
	play_sounds: true,
	roster_groups: true,
	show_controlbox_by_default: true,
	xhr_user_search: false
    })});




