<?php
/**
When setting configurations, remember that any app is also impacted by the configurations found in /core/config/config.php;
Most of the core configs can be overwritten here on the app level.

For example there's a constant in core config set for TIME_FORMAT

	if(!defined('TIME_FORMAT')) define('TIME_FORMAT', 'F j, Y g:ia'); 

If you want a different default time format for this app, set it below

	define('TIME_FORMAT', 'M j Y'); 

*/

# What is the name of this app?
	define('APP_NAME', 'Squirrel Cage');

# What is the app title tag line?
    define('APP_TAGLINE', 'Show us your nuts!');

# When email is sent out from the server, where should it come from?
# Ideally, this should match the domain name
	define('APP_EMAIL', 'webmaster@p2.websandbox.biz'); 

# Company / developer copyright boilerplate:
    define('COPYRIGHT', 'Jeffrey Linson 2013');

/*
A email designated to receive messages from the server. Examples:
 	* When there's a MySQL error on the live server it will send it to this email
 	* If you're BCCing yourself on outgoing emails you may want them to go there
 	* Logs, cron results, errors, etc.
 	
 	Some might want this to be the same as the APP_EMAIL, others might want to create a designated gmail address for it
*/ 	
	define('SYSTEM_EMAIL', 'jlinson@g.harvard.edu');
    //define('SYSTEM_EMAIL', 'webmaster@coldspringsoftware.com');	

# Default DB name for this app
	define('DB_NAME', 'websandb_p2_websandbox_biz'); 

# Timezone
	define('TIMEZONE', 'America/New_York');

# If your app is going to have outgoing emails, you should fill in your SMTP settings
# For this you could use gmail SMTP or something like http://sendgrid.com/
	define('SMTP_HOST', 'smtp.gmail.com');
	define('SMTP_USERNAME', 'smtp.gmail.com');
	define('SMTP_PASSWORD', 'Edmail4jbl13');

# For extra security, you might want to set different salts than what the core uses (32 to 64 char strings)
	define('PASSWORD_SALT', 'ZSCgofbx9)M)o|QPI-o:u)u~fA{-$%*N0r+g7M&$');
	define('TOKEN_SALT', 'XcZJ5$vRxDtJu2~2b^pCrA8UcA@5xcS_L0H|UG[_');
	
# To change the default Image / Avatar settings
	define('AVATAR_PATH', "/uploads/avatars/");
	define('SMALL_W', 200);
	define('SMALL_H', 200);
	define('PLACE_HOLDER_IMAGE', "/img/placeholder.png");
