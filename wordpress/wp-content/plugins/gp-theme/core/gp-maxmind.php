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
  		region VARCHAR(3) DEFAULT NULL,
  		city VARCHAR(45) DEFAULT NULL,
  		postalCode VARCHAR(7) DEFAULT NULL,
  		latitude DOUBLE DEFAULT NULL,
  		longitude DOUBLE DEFAULT NULL,
  		metroCode VARCHAR(3) DEFAULT NULL,
  		areaCode VARCHAR(3) DEFAULT NULL,
  		regionfipsCode VARCHAR(2) DEFAULT NULL,
  		regionName VARCHAR(255) DEFAULT NULL,
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
    
    # Delete blank United States "AP" and "AE" regions
    $query = "DELETE FROM " . $wpdb->base_prefix . "maxmind_geolitecitylocation WHERE country = 'US' AND ( region = 'AP' OR region = 'AE' );";
    
    $wpdb->query( $query );
    
    # Delete blank France "00" regions
    $query = "DELETE FROM " . $wpdb->base_prefix . "maxmind_geolitecitylocation WHERE country = 'FR' AND region = '00';";
    
    $wpdb->query( $query );
    
    # Delete blank United Kingdom "00" regions
    $query = "DELETE FROM " . $wpdb->base_prefix . "maxmind_geolitecitylocation WHERE country = 'GB' AND region = '00';";
    
    $wpdb->query( $query );
    
    # Delete blank New Zealand "00" regions
    $query = "DELETE FROM " . $wpdb->base_prefix . "maxmind_geolitecitylocation WHERE country = 'NZ' AND region = '00';";
    
    $wpdb->query( $query );
    
    # Delete blank Canada "00" regions
    $query = "DELETE FROM " . $wpdb->base_prefix . "maxmind_geolitecitylocation WHERE country = 'CA' AND region = '00';";
    
    $wpdb->query( $query );
    
    
    # Fix New Zealand "85" region
    $query = "UPDATE " . $wpdb->base_prefix . "maxmind_geolitecitylocation
    SET region='G1'
    WHERE locID='253032';";
    
    $wpdb->query( $query );
    
    # Fix Ireland regions (all)
    $IE_MAXMIND_FIX = array(
            '04' => 'C',
            '03' => 'CE',
            '02' => 'CN',
            '01' => 'CW',
            '07' => 'D',
            '06' => 'DL',
            '10' => 'G',
            '12' => 'KE',
            '13' => 'KK',
            '11' => 'KY',
            '18' => 'LD',
            '19' => 'LH',
            '16' => 'LK',
            '14' => 'LM',
            '15' => 'LS',
            '21' => 'MH',
            '22' => 'MN',
            '20' => 'MO',
            '23' => 'OY',
            '24' => 'RN',
            '25' => 'SO',
            '26' => 'TA',
            '27' => 'WD',
            '29' => 'WH',
            '31' => 'WW',
            '30' => 'WX'
    );
    
    foreach ( $IE_MAXMIND_FIX as $key => $value ) {
        $query = "UPDATE " . $wpdb->base_prefix . "maxmind_geolitecitylocation
        SET region='" . $value . "', regionfipsCode='" . $key . "'
        WHERE region='" . $key . "'
            AND country='IE';";
        
        $wpdb->query( $query );
    }
    
    # Fix United Kingdom regions (all)
    $query = "UPDATE " . $wpdb->base_prefix . "maxmind_geolitecitylocation
    SET region='Z5'
    WHERE region='A5'
    AND country='GB';";
    
    $wpdb->query( $query );
    
    $GB_MAXMIND_FIX = array(
        'T6' => 'ABD',
        'T5' => 'ABE',
        'T8' => 'AGB',
        'X1' => 'AGY',
        'T7' => 'ANS',
        'Q6' => 'ANT',
        'Q7' => 'ARD',
        'Q8' => 'ARM',
        'A4' => 'BAS',
        'A8' => 'BBD',
        'Z5' => 'BDF',
        'A1' => 'BDG',
        'B5' => 'BEN',
        'A6' => 'BEX',
        'R3' => 'BFS',
        'X3' => 'BGE',
        'X2' => 'BGW',
        'A7' => 'BIR',
        'B9' => 'BKM',
        'Q9' => 'BLA',
        'R1' => 'BLY',
        'B2' => 'BMH',
        'R2' => 'BNB',
        'A2' => 'BNE',
        'B6' => 'BNH',
        'A3' => 'BNS',
        'B1' => 'BOL',
        'A9' => 'BPL',
        'B3' => 'BRC',
        'B4' => 'BRD',
        'B8' => 'BRY',
        'B7' => 'BST',
        'C1' => 'BUR',
        'C3' => 'CAM',
        'X4' => 'CAY',
        'Z6' => 'CBF',
        'X6' => 'CGN',
        'R8' => 'CGV',
        'Z7' => 'CHE',
        'C5' => 'CHS',
        'Z8' => 'CHW',
        'R4' => 'CKF',
        'R7' => 'CKT',
        'C2' => 'CLD',
        'U1' => 'CLK',
        'R6' => 'CLR',
        'C9' => 'CMA',
        'C4' => 'CMD',
        'X7' => 'CMN',
        'C6' => 'CON',
        'C7' => 'COV',
        'X5' => 'CRF',
        'C8' => 'CRY',
        'R5' => 'CSR',
        'X8' => 'CWY',
        'D1' => 'DAL',
        'D3' => 'DBY',
        'X9' => 'DEN',
        'D2' => 'DER',
        'D4' => 'DEV',
        'S1' => 'DGN',
        'U2' => 'DGY',
        'D5' => 'DNC',
        'U3' => 'DND',
        'D6' => 'DOR',
        'R9' => 'DOW',
        'S6' => 'DRY',
        'D7' => 'DUD',
        'D8' => 'DUR',
        'D9' => 'EAL',
        'U4' => 'EAY',
        'U8' => 'EDH',
        'U5' => 'EDU',
        'U6' => 'ELN',
        'W8' => 'ELS',
        'E3' => 'ENF',
        'U7' => 'ERW',
        'E1' => 'ERY',
        'E4' => 'ESS',
        'E2' => 'ESX',
        'U9' => 'FAL',
        'S2' => 'FER',
        'V1' => 'FIF',
        'Y1' => 'FLN',
        'E5' => 'GAT',
        'V2' => 'GLG',
        'E6' => 'GLS',
        'E7' => 'GRE',
        'Y2' => 'GWN',
        'E9' => 'HAL',
        'F2' => 'HAM',
        'F6' => 'HAV',
        'E8' => 'HCK',
        'F7' => 'HEF',
        'F9' => 'HIL',
        'V3' => 'HLD',
        'F1' => 'HMF',
        'G1' => 'HNS',
        'F5' => 'HPL',
        'F8' => 'HRT',
        'F4' => 'HRW',
        'F3' => 'HRY',
        'G2' => 'IOW',
        'G3' => 'ISL',
        'V4' => 'IVC',
        'G4' => 'KEC',
        'G5' => 'KEN',
        'G6' => 'KHL',
        'G8' => 'KIR',
        'G7' => 'KTT',
        'G9' => 'KWL',
        'H2' => 'LAN',
        'H1' => 'LBH',
        'H4' => 'LCE',
        'H3' => 'LDS',
        'H5' => 'LEC',
        'H6' => 'LEW',
        'H7' => 'LIN',
        'H8' => 'LIV',
        'S4' => 'LMV',
        'H9' => 'LND',
        'S3' => 'LRN',
        'S5' => 'LSB',
        'I1' => 'LUT',
        'I2' => 'MAN',
        'I5' => 'MDB',
        'I3' => 'MDW',
        'S7' => 'MFT',
        'I6' => 'MIK',
        'V5' => 'MLN',
        'Y4' => 'MON',
        'I4' => 'MRT',
        'V6' => 'MRY',
        'Y3' => 'MTY',
        'S8' => 'MYL',
        'V7' => 'NAY',
        'J6' => 'NBL',
        'T2' => 'NDN',
        'J2' => 'NEL',
        'I7' => 'NET',
        'I9' => 'NFK',
        'J8' => 'NGM',
        'V8' => 'NLK',
        'J3' => 'NLN',
        'J4' => 'NSM',
        'T1' => 'NTA',
        'J1' => 'NTH',
        'Y5' => 'NTL',
        'J9' => 'NTT',
        'J5' => 'NTY',
        'I8' => 'NWM',
        'Y6' => 'NWP',
        'J7' => 'NYK',
        'S9' => 'NYM',
        'K1' => 'OLD',
        'T3' => 'OMH',
        'V9' => 'ORK',
        'K2' => 'OXF',
        'Y7' => 'PEM',
        'W1' => 'PKN',
        'K4' => 'PLY',
        'K5' => 'POL',
        'K6' => 'POR',
        'Y8' => 'POW',
        'K3' => 'PTE',
        'K9' => 'RCC',
        'L2' => 'RCH',
        'Y9' => 'RCT',
        'K8' => 'RDB',
        'K7' => 'RDG',
        'W2' => 'RFW',
        'L1' => 'RIC',
        'L3' => 'ROT',
        'L4' => 'RUT',
        'L7' => 'SAW',
        'W4' => 'SAY',
        'T9' => 'SCB',
        'N5' => 'SFK',
        'L8' => 'SFT',
        'M6' => 'SGC',
        'L9' => 'SHF',
        'N1' => 'SHN',
        'L6' => 'SHR',
        'N2' => 'SKP',
        'L5' => 'SLF',
        'M1' => 'SLG',
        'W5' => 'SLK',
        'N6' => 'SND',
        'M2' => 'SOL',
        'M3' => 'SOM',
        'M5' => 'SOS',
        'N7' => 'SRY',
        'T4' => 'STB',
        'N4' => 'STE',
        'W6' => 'STG',
        'M4' => 'STH',
        'N8' => 'STN',
        'M9' => 'STS',
        'N3' => 'STT',
        'M7' => 'STY',
        'Z1' => 'SWA',
        'N9' => 'SWD',
        'M8' => 'SWK',
        'O1' => 'TAM',
        'O2' => 'TFW',
        'O3' => 'THR',
        'O4' => 'TOB',
        'Z2' => 'TOF',
        'O6' => 'TRF',
        'O5' => 'TWH',
        'Z3' => 'VGL',
        'P3' => 'WAR',
        'P4' => 'WBK',
        'W7' => 'WDU',
        'O9' => 'WFT',
        'P7' => 'WGN',
        'P8' => 'WIL',
        'O7' => 'WKF',
        'O8' => 'WLL',
        'W9' => 'WLN',
        'Q3' => 'WLV',
        'P1' => 'WND',
        'P9' => 'WNM',
        'Q2' => 'WOK',
        'Q4' => 'WOR',
        'Q1' => 'WRL',
        'P2' => 'WRT',
        'Z4' => 'WRX',
        'P5' => 'WSM',
        'P6' => 'WSX',
        'Q5' => 'YOR',
        'W3' => 'ZET'
    );
    
    // NOTE: The following regions are incorrect references and have NOT been fixed yet and have the wrong codes; 17,87,03,37,43,45,28,90.
    // You can find each city (161!) in Wikipedia and update the results manually.
    foreach ( $GB_MAXMIND_FIX as $key => $value ) {
        $query = "UPDATE " . $wpdb->base_prefix . "maxmind_geolitecitylocation
        SET region='" . $value . "', regionfipsCode='" . $key . "'
        WHERE region='" . $key . "'
        AND country='GB';";
    
        $wpdb->query( $query );
    }
    
    
    # Fix ISO regional data - AU, IN, FR, NZ
    $query = "REPLACE INTO " . $wpdb->base_prefix . "maxmind_geolitecitylocation(locID, country, region, city, postalCode, latitude, longitude, metroCode, areaCode, regionfipsCode, regionName)
        SELECT locID, " . $wpdb->base_prefix . "maxmind_geolitecitylocation.country, " . $wpdb->base_prefix . "debian_iso_3166_2.code, city, postalCode, latitude, longitude, metroCode, areaCode, region, " . $wpdb->base_prefix . "debian_iso_3166_2.name
        FROM " . $wpdb->base_prefix . "maxmind_geolitecitylocation 
        LEFT OUTER JOIN " . $wpdb->base_prefix . "geonames_admin1codesascii 
            ON concat(country, '.', region) = " . $wpdb->base_prefix . "geonames_admin1codesascii.code 
        LEFT OUTER JOIN " . $wpdb->base_prefix . "debian_iso_3166_2
            ON " . $wpdb->base_prefix . "geonames_admin1codesascii.name = " . $wpdb->base_prefix . "debian_iso_3166_2.name
        WHERE 
            region != '' 
            AND ( " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'AU' 
            OR " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'IN'
            OR " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'FR'
            OR " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'NZ' );";
    
    $wpdb->query( $query );
    
    
    # Fix ISO regional data - US, CA (Note: fips codes now need to be fixed - not important)
    $query = "REPLACE INTO " . $wpdb->base_prefix . "maxmind_geolitecitylocation(locID, country, region, city, postalCode, latitude, longitude, metroCode, areaCode, regionfipsCode, regionName)
        SELECT locID, " . $wpdb->base_prefix . "maxmind_geolitecitylocation.country, " . $wpdb->base_prefix . "debian_iso_3166_2.code, city, postalCode, latitude, longitude, metroCode, areaCode, region, " . $wpdb->base_prefix . "debian_iso_3166_2.name
        FROM wp_maxmind_geolitecitylocation
        LEFT OUTER JOIN " . $wpdb->base_prefix . "debian_iso_3166_2
            ON region = " . $wpdb->base_prefix . "debian_iso_3166_2.code AND " . $wpdb->base_prefix . "maxmind_geolitecitylocation.country = " . $wpdb->base_prefix . "debian_iso_3166_2.country
        WHERE
            region != ''
            AND ( " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'US' 
            OR " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'CA' );";
    
    $wpdb->query( $query );
    
    
    # Fix ISO regional data - GB, IE
    $query = "REPLACE INTO " . $wpdb->base_prefix . "maxmind_geolitecitylocation(locID, country, region, city, postalCode, latitude, longitude, metroCode, areaCode, regionfipsCode, regionName)
        SELECT locID, " . $wpdb->base_prefix . "maxmind_geolitecitylocation.country, " . $wpdb->base_prefix . "debian_iso_3166_2.code, city, postalCode, latitude, longitude, metroCode, areaCode, regionfipsCode, " . $wpdb->base_prefix . "debian_iso_3166_2.name
        FROM wp_maxmind_geolitecitylocation
        LEFT OUTER JOIN " . $wpdb->base_prefix . "debian_iso_3166_2
            ON region = " . $wpdb->base_prefix . "debian_iso_3166_2.code AND " . $wpdb->base_prefix . "maxmind_geolitecitylocation.country = " . $wpdb->base_prefix . "debian_iso_3166_2.country
        WHERE
            region != ''
            AND ( " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'IE'
            OR " . $wpdb->base_prefix . "debian_iso_3166_2.country = 'GB' );";
    
    $wpdb->query( $query );
    
    
    add_option( "GP_MAXMIND_VERSION", GP_MAXMIND_VERSION );
    add_option( "gp_maxmind_lastupdated", time() );
}

?>
