<?php

class Geo {

	private static $location;

	public static function init() {
	    global $wpdb;

	    #$clientip = false;                   // Production
	    $clientip = '58.109.255.255';     // AU example
	    #$clientip = '62.49.255.255';      // UK example
	    #$clientip = '16.255.255.255';     // US example
	    #$clientip = '24.71.255.255';      // CA example
	    #$clientip = '115.189.255.255';    // NZ example
	    #$clientip = '188.141.127.255';    // IE example
	    #$clientip = '60.243.255.255';     // IN example
	    #$clientip = '62.161.255.255';     // FR example
	    
	    $location = getCurrentLocation( $clientip );
		
		self::$location = $location;
	}
	
	public static function getCurrentLocation() {
		self::init();
		return self::$location;
	}
	
}

?>