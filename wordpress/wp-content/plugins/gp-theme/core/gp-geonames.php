<?php
/*
 * NOTES:
* http://download.geonames.org/export/dump/
* http://forum.geonames.org/gforum/posts/list/45/732.page
* http://forum.geonames.org/gforum/posts/list/15/80.page
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

    return;
}

function gp_core_import_geonames_citiesdata() {
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
}
?>