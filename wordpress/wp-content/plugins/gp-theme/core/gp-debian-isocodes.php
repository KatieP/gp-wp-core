<?php
/*
 * GIT SOURCE:
 * http://anonscm.debian.org/gitweb/?p=iso-codes/iso-codes.git
 * git clone git://anonscm.debian.org/iso-codes/iso-codes.git
 */

function gp_core_create_debian_isocodes_tables() {
    global $wpdb, $gp;

    if ( !empty($wpdb->charset) ) {
        $charset_collate = "DEFAULT CHARACTER SET " . $wpdb->charset;
    }

    $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "debian_iso_3166_2;" );

    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "debian_iso_3166_2 (
        id VARCHAR(10) PRIMARY KEY,
        code VARCHAR(10),
        name VARCHAR(255),
        subset VARCHAR(255),
        country VARCHAR(2)
        ) ENGINE=MyISAM " . $charset_collate . ";";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    add_option( "GP_DEBIAN_ISOCODES_VERSION", GP_DEBIAN_ISOCODES_VERSION );
}

function gp_core_import_debian_isocodes_data() {
    global $wpdb;

    if ( file_exists( GP_PLUGIN_DIR . '/import/iso_3166_2_iso_3166_2.xml' ) ) {
        $xml = simplexml_load_file( GP_PLUGIN_DIR . '/import/iso_3166_2_iso_3166_2.xml' );
    } else {
        return;
    }
    
    foreach ($xml->iso_3166_country as $country) {
        if ( isset( $country->iso_3166_subset->iso_3166_2_entry ) ) {
            foreach ( $country->iso_3166_subset->iso_3166_2_entry as $subset ) {
                $code = substr( $subset['code'], strlen( $country['code'] . "-" ), strlen( $subset['code'] ) );
    
                $wpdb->query( $wpdb->prepare( 
                    "INSERT INTO " . $wpdb->base_prefix . "debian_iso_3166_2
                    ( id, code, name, subset, country ) 
                    VALUES( %s, %s, %s, %s, %s );", 
                    $subset['code'], 
                    $code, 
                    $subset['name'], 
                    $country->iso_3166_subset['type'], 
                    $country['code']
                ) );
            }
        }
    }
}
?>