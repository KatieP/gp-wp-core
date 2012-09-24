<?php
/*
 * NOTES:
* http://download.geonames.org/export/dump/
* http://forum.geonames.org/gforum/posts/list/45/732.page
* http://forum.geonames.org/gforum/posts/list/15/80.page
*
* State Abbreviations:
* http://forum.geonames.org/gforum/posts/list/516.page
* http://www.commondatahub.com/state_source.jsp
* http://anonscm.debian.org/gitweb/?p=iso-codes/iso-codes.git
* http://stackoverflow.com/questions/1450744/source-of-iso-data-in-xml-format
*
* Future reference:
// assuming file.zip is in the same directory as the executing script.
$file = 'file.zip';

// get the absolute path to $file
$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

$zip = new ZipArchive;
$res = $zip->open($file);
if ($res === TRUE) {
// extract it to the path we determined above
$zip->extractTo($path);
$zip->close();
echo "WOOT! $file extracted to $path";
} else {
echo "Doh! I couldn't open $file";
}
*/

function gp_core_create_geonames_tables() {
    global $wpdb, $gp;

    if ( !empty($wpdb->charset) ) {
        $charset_collate = "DEFAULT CHARACTER SET " . $wpdb->charset;
    }

    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_geoname;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_geoname (
        geonameid INT PRIMARY KEY, 
        name VARCHAR(200), 
        asciiname VARCHAR(200), 
        alternatenames VARCHAR(4000), 
        latitude DECIMAL(10,7), 
        longitude DECIMAL(10,7), 
        fclass VARCHAR(1), 
        fcode VARCHAR(10), 
        country VARCHAR(2), 
        cc2 VARCHAR(60), 
        admin1 VARCHAR(20), 
        admin2 VARCHAR(80), 
        admin3 VARCHAR(20), 
        admin4 VARCHAR(20), 
        population BIGINT, 
        elevation INT, 
        gtopo30 INT, 
        timezone VARCHAR(40), 
        modified DATE
        ) ENGINE=MyISAM " . $charset_collate . ";";
    
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_alternatenames;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_alternatenames (
        alternatenameid INT PRIMARY KEY, 
        geonameid INT, 
        isolanguage VARCHAR(7), 
        alternatename VARCHAR(200), 
        ispreferredname BOOLEAN, 
        isshortname BOOLEAN,
        iscolloquial BOOLEAN,
        ishistoric BOOLEAN
        ) ENGINE=MyISAM " . $charset_collate . ";";
        
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_countryinfo;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_countryinfo ( 
        iso_alpha2 VARCHAR(2), 
        iso_alpha3 VARCHAR(3), 
        iso_numeric INT, 
        fips_code VARCHAR(3), 
        name VARCHAR(200), 
        capital VARCHAR(200), 
        areainsqkm DOUBLE, 
        population INT, 
        continent VARCHAR(2), 
        tld VARCHAR(3), 
        currency VARCHAR(3), 
        currencyName VARCHAR(20), 
        Phone VARCHAR(10), 
        postalCodeFormat VARCHAR(20), 
        postalCodeRegex VARCHAR(20),
        languages VARCHAR(200), 
        geonameId INT,  
        neighbours VARCHAR(20), 
        equivalentFipsCode VARCHAR(10) 
        ) ENGINE=MyISAM " . $charset_collate . ";";
    
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_isolanguagecodes;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_isolanguagecodes ( 
        iso_639_3 VARCHAR(4), 
        iso_639_2 VARCHAR(50), 
        iso_639_1 VARCHAR(50), 
        language_name VARCHAR(200) 
        ) ENGINE=MyISAM " . $charset_collate . ";";
    
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_admin1codesascii;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_admin1codesascii ( 
        code VARCHAR(6), 
        name TEXT, 
        nameAscii TEXT, 
        geonameid INT 
        ) ENGINE=MyISAM " . $charset_collate . ";";
    
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_admin2codes;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_admin2codes (
        code VARCHAR(32) DEFAULT NULL,
        name_local VARCHAR(100),
        name VARCHAR(100) NOT NULL,
        geonameid INT(11) NOT NULL
        ) ENGINE=MyISAM " . $charset_collate . ";";
    
    /*
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_featureCodes;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_featurecodes ( 
        code VARCHAR(7), 
        name VARCHAR(200), 
        description TEXT 
        ) ENGINE=MyISAM " . $charset_collate . ";";
    */
    
    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "geonames_timezones;" );
    
    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "geonames_timezones (
        countrycode VARCHAR(2),
        timezoneid VARCHAR(100), 
        gmtoffset DECIMAL(3,1), 
        dstoffset DECIMAL(3,1),
        rawoffset DECIMAL(3,1) 
        ) ENGINE=MyISAM " . $charset_collate . ";";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( "GP_DB_VERSION", GP_DB_VERSION );
}

function gp_core_import_geonames_citiesdata() {
    global $wpdb;
    
    if ( !get_option( 'GP_GEONAMES_IMPORTDAYS' ) ) {
        update_option( 'GP_GEONAMES_IMPORTDAYS', 30 );
    }

    if ( !get_option( 'GP_GEONAMES_LASTIMPORT' ) ) {
        update_option( 'GP_GEONAMES_LASTIMPORT', time() );
    }

    $destination = GP_PLUGIN_DIR . "/import/";
    $source_url = "http://download.geonames.org/export/dump";
    $remote_files = array(
            "admin1CodesASCII.txt",
            "admin2Codes.txt",
            "countryInfo.txt",
            "timeZones.txt",
            "alternateNames.zip",
            "allCountries.zip",
            "allCountries.tar.gz"
    );

    $url = parse_url( $source_url );

    if ( $url === false ) {
        return;
    }

    if ( $url['scheme'] != "http" ) {
        return;
    }

    $url = $url['scheme'] . "://" . $url['host'] . $url['path'];
    // should check scheme/host/path exist
    $url .= ( strpos( $url, '/' ) === false ) ? '' : '/';
    // should re-attach $export_location fragment if exists and leave off if file name has fragment, etc, instead.

    foreach ($remote_files as $remote_file) {
        $source = $url . $remote_file;
        uploadRemoteFile($source, $destination, array("ZIP", "TXT"), true, false, true, 2, "http");
    }
     
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/allCountries.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_geoname
    (geonameid, name, asciiname, alternatenames, latitude, longitude, fclass, fcode, country, cc2, admin1, admin2, admin3, admin4, population, elevation, gtopo30, timezone, modified);";
    
    $wpdb->query( $query );
    
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/alternateNames.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_alternatenames
    (alternatenameid, geonameid, isolanguage, alternatename, ispreferredname, isshortname, iscolloquial, ishistoric);";
    
    $wpdb->query( $query );
    
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/iso-languagecodes.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_isolanguagecodes IGNORE 1 LINES
    (iso_639_3, iso_639_2, iso_639_1, language_name);";
    
    $wpdb->query( $query );
    
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/admin1CodesASCII.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_admin1codesascii 
    (code, name, nameAscii, geonameid);";
    
    $wpdb->query( $query );
    
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/admin2Codes.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_admin2codes 
    (code, name_local, name, geonameid);";
    
    $wpdb->query( $query );
    
    /*
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/featureCodes.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_featurecodes
    (code, name, description);";
    
    $wpdb->query( $query );
    */
    
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/timeZones.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_timezones IGNORE 1 LINES
    (countrycode, timezoneid, gmtoffset, dstoffset, rawoffset)";
    
    $wpdb->query( $query );
    
    $query = "LOAD data LOCAL INFILE '" . GP_PLUGIN_DIR . "/import/countryInfo.txt' 
    INTO TABLE " . $wpdb->base_prefix . "geonames_countryinfo IGNORE 51 LINES
    (iso_alpha2, iso_alpha3, iso_numeric, fips_code, name, capital, areaInSqKm, population, continent, languages, currency, geonameId);";
    
    $wpdb->query( $query );
    
    # Fix Australian Capital Territory, Australia
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Australian Capital Territory', nameAscii='Australian Capital Territory'
    WHERE code='AU.01';";
    
    $wpdb->query( $query );
    
    # Fix West Bengal, India
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='West Bengal', nameAscii='West Bengal'
    WHERE code='IN.28';";
    
    $wpdb->query( $query );
    
    # Fix Dehli, India
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Delhi', nameAscii='Delhi'
    WHERE code='IN.07';";
    
    $wpdb->query( $query );
    
    # Fix Puducherry, India
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Puducherry', nameAscii='Puducherry'
    WHERE code='IN.22';";
    
    $wpdb->query( $query );
    
    # Fix Daman and Diu, India
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Daman and Diu', nameAscii='Daman and Diu'
    WHERE code='IN.32';";
    
    $wpdb->query( $query );
    
    # Fix Bretagne, France
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Bretagne', nameAscii='Bretagne'
    WHERE code='FR.A2';";
    
    $wpdb->query( $query );
    
    # Fix Corse, France
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Corse', nameAscii='Corse'
    WHERE code='FR.A5';";
    
    $wpdb->query( $query );
    
    # Fix Gisborne District, New Zealand
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Gisborne District', nameAscii='Gisborne District'
    WHERE code='NZ.F1';";
    
    $wpdb->query( $query );
    
    # Fix Marlborough District, New Zealand
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Marlborough District', nameAscii='Marlborough District'
    WHERE code='NZ.F4';";
    
    $wpdb->query( $query );
    
    # Fix Nelson City, New Zealand
    $query = "UPDATE " . $wpdb->base_prefix . "geonames_admin1codesascii
    SET name='Nelson City', nameAscii='Nelson City'
    WHERE code='NZ.F5';";
    
    $wpdb->query( $query );
    
    
    add_option( "GP_GEONAMES_VERSION", GP_GEONAMES_VERSION );
    add_option( "gp_geonames_lastupdated", time() );
    
}
?>