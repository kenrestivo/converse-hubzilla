require(['converse'], function (converse) {
    converse.initialize({
	bosh_service_url: 'https://hub.spaz.org:5281/http-bind/',
	websocket_url: 'wss://hub.spaz.org:5281/websocket/',
	domain_placeholder: 'hub.spaz.org', 
	keepalive: true,
	animate: false,
	autologin: false, // will be true once jid is populated, WHEN it is populated
	// TODO: provide jid, password, and auto-log them in (pconfig, auto-populate from db)
	message_carbons: true,
	debug: false, 
	play_sounds: true, // TODO: let the user decide (pconfig)
	roster_groups: true,
	show_controlbox_by_default: false,
	xhr_user_search: false
    })});




