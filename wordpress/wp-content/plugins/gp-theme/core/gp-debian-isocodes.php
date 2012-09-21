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
        parent VARCHAR(10),
        subset VARCHAR(255),
        subset_plural VARCHAR(255),
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
            foreach ( $country->iso_3166_subset as $subset ) {
                $subset_plural = $subset['type'];
                if ( substr( $subset_plural, strlen( $subset_plural ) - 2, strlen( $subset_plural ) ) == "ey" ) { $subset_plural = $subset_plural . "s"; }
                if ( substr( $subset_plural, strlen( $subset_plural ) - 1, strlen( $subset_plural ) ) == "y" ) { $subset_plural = substr( $subset_plural, 0, strlen( $subset_plural ) - 1 ) . "ies"; }
                if ( substr( $subset_plural, strlen( $subset_plural ) - 1, strlen( $subset_plural ) ) == "h" ) {$subset_plural = $subset_plural . "es"; }
                if ( substr( $subset_plural, strlen( $subset_plural ) - 1, strlen( $subset_plural ) ) == "e" || substr( $subset_plural, strlen( $subset_plural ) - 1, strlen( $subset_plural ) ) != "s" ) { $subset_plural = $subset_plural . "s"; }
                
                $parent = "";
                
                # Fix United Kingdom parents
                if ( $country['code'] == "GB" ) {
                    switch ( strtolower( $subset['type'] ) ) {
                        case "council area":
                            $parent = "SCT";
                            break;
                        case "unitary authority (wales)":
                            $parent = "WLS";
                            $subset_plural = "unitary authorities";
                            break;
                        case "district council area":
                            $parent = "NIR";
                            break;
                        case "two-tier county":
                        case "london borough":
                        case "metropolitan district":
                            $parent = "ENG";
                            break;
                        case "unitary authority (england)":
                            $parent = "ENG";
                            $subset_plural = "unitary authorities";
                            break;
                        case "country":
                            $parent = "GBN;UKM";
                            break;
                        case "province":
                            $parent = "UKM";
                            break;
                    }
                }

                foreach ( $subset->iso_3166_2_entry as $entry ) {
                    $code = substr( $entry['code'], strlen( $country['code'] . "-" ), strlen( $entry['code'] ) );
                    
                    # Fix United Kingdom parents
                    if ( $country['code'] == "GB" && ( $code == "GBN" || $code == "EAW" ) ) { $parent = "UKM"; }
                    if ( $country['code'] == "GB" && ( $code == "ENG" || $code == "WLS" ) ) { $parent = "EAW;UKM"; }
                    if ( $country['code'] == "GB" && $code == "LND" ) { $parent = "ENG"; }
                    
                    # Fix Welsh names
                    if ( $entry['code'] == "GB-RCT" ) { $entry['name'] = "Rhondda Cynon Taf"; }
                    if ( $parent == "WLS" && strpos( $entry['name'], ";" ) >= 1 ) {
                        $entry['name'] = str_replace( ";", " (", $entry['name'] ) . ")";
                    }
                    
                    if ( $parent == "" ) { $parent = $entry['parent']; }
                    
                    $wpdb->query( $wpdb->prepare( 
                        "INSERT INTO " . $wpdb->base_prefix . "debian_iso_3166_2
                        ( id, code, name, parent, subset, subset_plural, country ) 
                        VALUES( %s, %s, %s, %s, %s, %s, %s );", 
                        $entry['code'], 
                        $code, 
                        $entry['name'], 
                        $parent,
                        strtolower( $subset['type'] ),
                        strtolower( $subset_plural ), 
                        $country['code']
                    ) );
                }
            }
        }
    }
}
?>