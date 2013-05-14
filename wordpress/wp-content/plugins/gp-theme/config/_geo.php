<?php
class Geo {

	private static $current_location, $user_location;

	public static function init() {
	    global $wpdb;
	    
	    $clientip = false;                // Production
	    #$clientip = '58.109.255.255';     // AU example
	    #$clientip = '62.49.255.255';      // UK example
	    #$clientip = '16.255.255.255';     // US example
	    #$clientip = '24.71.255.255';      // CA example
	    #$clientip = '115.189.255.255';    // NZ example
	    #$clientip = '188.141.127.255';    // IE example
	    #$clientip = '60.243.255.255';     // IN example
	    #$clientip = '62.161.255.255';     // FR example
	    
	    $current_location = getLocationByIP( $clientip );
	    
	    $site_editions = Site::getEditions();
	    
	    $isEdition = false;
	    foreach ( $site_editions as $edition ) {
	        if ( $edition['iso2'] == $current_location['country_iso2'] ) {
	            $isEdition = true;
	        }
	    }
	    
	    if ( $current_location == null || !$isEdition ) {
	        $current_location = array(
	            "country" => "United States", 
	            "country_iso2" => "US"
	        );
	    }
		
		self::$current_location = $current_location;
		
		
		$user_location = false;
		
		self::$user_location = $user_location;
	}
	
	public static function getCurrentLocation() {
		self::init();
		return self::$current_location;
	}
	
	public static function getUserLocation() {
	    self::init();
	    return self::$user_location;
	}
	
}
?>