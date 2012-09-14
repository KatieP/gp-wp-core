<?php
/*
 * NOTES:
* http://www.atomodo.com/code/how-to-really-import-the-maxmind-geoip-free-country-csv-file-into-mysql
* http://www.netmagazine.com/tutorials/getting-started-html5-geolocation
* http://squirrelshaterobots.com/programming/php/geoip-in-php-with-the-new-php-geoipo-extension/
* http://php.net/manual/en/ref.zip.php
* http://phix.me/geodns/
*/

function gp_core_create_maxmind_tables() {
    global $wpdb, $gp;

    if ( !empty($wpdb->charset) ) {
        $charset_collate = "DEFAULT CHARACTER SET " . $wpdb->charset;
    }

    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "maxmind_geolitecityblocks_ipv4;" );

    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "maxmind_geolitecityblocks_ipv4 (
        startIPNum INT(10) UNSIGNED NOT NULL,
        endIPNum INT(10) UNSIGNED NOT NULL,
  		locID INT(10) UNSIGNED NOT NULL,
  		PRIMARY KEY (startIPNum, endIPNum)
  		) ENGINE=MyISAM " . $charset_collate . " PACK_KEYS=1 DELAY_KEY_WRITE=1;";

    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "maxmind_geolitecitylocation;" );

    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "maxmind_geolitecitylocation (
        locID INT(10) UNSIGNED NOT NULL,
        country VARCHAR(2) DEFAULT NULL,
  		region VARCHAR(2) DEFAULT NULL,
  		city VARCHAR(45) DEFAULT NULL,
  		postalCode VARCHAR(7) DEFAULT NULL,
  		latitude DOUBLE DEFAULT NULL,
  		longitude DOUBLE DEFAULT NULL,
  		metroCode VARCHAR(3) DEFAULT NULL,
  		areaCode VARCHAR(3) DEFAULT NULL,
  		PRIMARY KEY (locID),
  		KEY Index_Country (country)
  		) ENGINE=MyISAM " . $charset_collate . " ROW_FORMAT=FIXED;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( "GP_DB_VERSION", GP_DB_VERSION );
}

function gp_core_import_maxmind_citiesdata() {
    global $wpdb;

    $query = "DROP FUNCTION IF EXISTS IPTOLOCID ;";

    $wpdb->query( $query );

    /* Giving up on this!
     $query = "DELIMITER $$
    CREATE FUNCTION \"s1-wordpress\".IPTOLOCID(ip VARCHAR(15)) RETURNS int(10) unsigned
    BEGIN
        DECLARE ipn INTEGER UNSIGNED ;
        DECLARE locID_var INTEGER ;
        IF ip LIKE '192.168.%' OR ip LIKE '10.%' THEN
            RETURN 0 ;
        END IF;
        SET ipn = INET_ATON(ip) ;
        SELECT locID INTO locID_var
            FROM wp_maxmind_geolitecityblocks_ipv4
            INNER JOIN
                (SELECT MAX(startIPNum) AS start
                FROM wp_maxmind_geolitecityblocks_ipv4
                WHERE startIPNum <= ipn) AS s
            ON (startIPNum = s.start)
            WHERE endIPNum >= ipn;
        RETURN locID_var ;
        END ;$$
    DELIMITER ;";

    $wpdb->query( $query );
    */

    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/GeoLiteCity-Blocks.csv'
        INTO TABLE " . $wpdb->base_prefix . "maxmind_geolitecityblocks_ipv4
        FIELDS TERMINATED BY ','
        OPTIONALLY ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'
        IGNORE 2 LINES;";

    $wpdb->query( $query );

    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/GeoLiteCity-Location.csv'
        INTO TABLE " . $wpdb->base_prefix . "maxmind_geolitecitylocation
        FIELDS TERMINATED BY ','
        OPTIONALLY ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'
        IGNORE 2 LINES;";

    $wpdb->query( $query );

    add_option( "GP_MAXMIND_VERSION", GP_MAXMIND_VERSION );
    add_option( "gp_maxmind_lastupdated", time() );
}

?>
