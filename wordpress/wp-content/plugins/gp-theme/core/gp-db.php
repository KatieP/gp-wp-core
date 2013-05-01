<?php 

function gp_core_create_gp_tables() {
    global $wpdb, $gp;

    if ( !empty($wpdb->charset) ) {
        $charset_collate = " DEFAULT CHARACTER SET " . $wpdb->charset;
    }

    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_drinquiries (
            ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            org_name VARCHAR(255) NOT NULL,
            org_url VARCHAR(255) NOT NULL,
            org_street VARCHAR(255) NOT NULL,
            org_city VARCHAR(255) NOT NULL,
            org_postcode VARCHAR(4) NOT NULL,
            org_state VARCHAR(3) NOT NULL,
            org_twitter VARCHAR(255) NOT NULL,
            org_facebook VARCHAR(255) NOT NULL,
            org_description LONGTEXT NOT NULL,
            contact_firstname VARCHAR(255) NOT NULL,
            contact_lastname VARCHAR(255) NOT NULL,
            contact_email VARCHAR(255) NOT NULL,
            contact_phone VARCHAR(255) NOT NULL,
            org_storeurl VARCHAR(255) NOT NULL,
            dir_images VARCHAR(1000) NOT NULL,
            dsc_biodiversity LONGTEXT NOT NULL,
            dsc_carbon LONGTEXT NOT NULL,
            dsc_history LONGTEXT NOT NULL,
            dsc_energy LONGTEXT NOT NULL,
            dsc_sustainability LONGTEXT NOT NULL,
            dsc_envfacts LONGTEXT NOT NULL,
            dsc_materials LONGTEXT NOT NULL,
            dsc_social LONGTEXT NOT NULL,
            dsc_packaging LONGTEXT NOT NULL,
            dsc_recyclability LONGTEXT NOT NULL,
            dsc_recycledcontent LONGTEXT NOT NULL,
            dsc_water LONGTEXT NOT NULL,
            certs_list VARCHAR(255) NOT NULL,
            FORM_STATE VARCHAR(255) NOT NULL,
            UID BIGINT(20) NOT NULL,
            CREATED VARCHAR(255) NOT NULL,
            LAST_ACTIVITY VARCHAR(255) NOT NULL,
            SUBMITTED VARCHAR(255) NOT NULL,
            USER_AGENT VARCHAR(255) NOT NULL,
            REMOTE_ADDR VARCHAR(255) NOT NULL,
            HTTP_REFERER VARCHAR(255) NOT NULL,
            session_id VARCHAR(255) NOT NULL,
            SITE_ID BIGINT(20) NOT NULL
        )" . $charset_collate. ";";

    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_adinquiries (
            ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            org_name VARCHAR(255) NOT NULL,
            notes LONGTEXT NOT NULL,
            contact_firstname VARCHAR(255) NOT NULL,
            contact_lastname VARCHAR(255) NOT NULL,
            contact_email VARCHAR(255) NOT NULL,
            contact_phone VARCHAR(255) NOT NULL,
            ad_opts VARCHAR(255) NOT NULL,
            FORM_STATE VARCHAR(255) NOT NULL,
            UID BIGINT(20) NOT NULL,
            CREATED VARCHAR(255) NOT NULL,
            LAST_ACTIVITY VARCHAR(255) NOT NULL,
            SUBMITTED VARCHAR(255) NOT NULL,
            USER_AGENT VARCHAR(255) NOT NULL,
            REMOTE_ADDR VARCHAR(255) NOT NULL,
            HTTP_REFERER VARCHAR(255) NOT NULL,
            session_id VARCHAR(255) NOT NULL,
            SITE_ID BIGINT(20) NOT NULL
        )" . $charset_collate. ";";

    #$sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_cpinquiries (ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, UID BIGINT(20) NOT NULL)" . $charset_collate. ";";

    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_feedback (
            ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            UID BIGINT(20) NOT NULL,
            contact_email VARCHAR(255) NOT NULL,
            feedback_content LONGTEXT NOT NULL,
            FORM_STATE VARCHAR(255) NOT NULL,
            UID BIGINT(20) NOT NULL,
            CREATED VARCHAR(255) NOT NULL,
            LAST_ACTIVITY VARCHAR(255) NOT NULL,
            SUBMITTED VARCHAR(255) NOT NULL,
            USER_AGENT VARCHAR(255) NOT NULL,
            REMOTE_ADDR VARCHAR(255) NOT NULL,
            HTTP_REFERER VARCHAR(255) NOT NULL,
            session_id VARCHAR(255) NOT NULL,
            SITE_ID BIGINT(20) NOT NULL
        )" . $charset_collate. ";";

    $sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_ecocerts (
            ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      		org_name VARCHAR(255) NOT NULL,
      		org_abbr VARCHAR(255) NOT NULL,
      		org_logo_full VARCHAR(255) NOT NULL
      		org_logo_thumb VARCHAR(255) NOT NULL,
      		org_contact_email VARCHAR(255) NOT NULL,
      		org_siteurl VARCHAR(255) NOT NULL,
      		org_reach VARCHAR(255) NOT NULL,
      		SITE_ID BIGINT(20) NOT NULL
  		)" . $charset_collate . ";";
    
    $sql[] = "ALTER TABLE " . $wpdb->base_prefix . "posts (
    		  ADD COLUMN post_latitude DECIMAL(10, 8) NOT NULL 
    		  );";
    
    $sql[] = "ALTER TABLE " . $wpdb->base_prefix . "posts (
    		  ADD COLUMN post_longitude DECIMAL(11, 8) NOT NULL 
    		  );";    

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option("GP_DB_VERSION", GP_DB_VERSION);
}
?>
