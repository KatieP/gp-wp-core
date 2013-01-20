<?php
class Site {

	private static $posttypes, $editions;

	public static function init() {
	    global $wpdb;
	    
	    /** Serviced Post Types **/
	    /* WARNING!
	     * Since ALL post types for EVERY edition (country) must be exposed for 
	     * SEO and search engine crawlers we need to return this information for
	     * our url rewrite rules (see plugin function 'gp_rewrite_rules'). We can't
	     * load every edition configuration file to access this information hence
	     * this hack.
	     * 
	     * WARNING!
	     * Both 'id' and 'slug' must match the values specified in the edition
	     * (country) configuration file. E.g configs/AU.php and configs/_default.php
	     * In other words SLUGS CANNOT BE NOT UNIQUE for each edition :(
	     * 
	     * NOTE
	     * In the future to solve all these problems we could generate mod_rewrite 
	     * rules in our .htaccess script but at the time of writing Wordpress failed 
	     * to respect these rules. gp-form-urlrules.php was an attempt at this.
	     */
	    $posttypes = array(
            array('id' => 'gp_news', 'slug' => 'news', 'title' => 'News'), 
            array('id' => 'gp_events', 'slug' => 'events', 'title' => 'Events'),
            array('id' => 'gp_jobs', 'slug' => 'jobs', 'title' => 'Jobs'), 
            array('id' => 'gp_competitions', 'slug' => 'competitions', 'title' => 'Competitions'), 
            array('id' => 'gp_people', 'slug' => 'people', 'title' => ''), 
            array('id' => 'gp_katiepatrick', 'slug' => 'katie-patrick', 'title' => 'Katie Patrick'), 
            array('id' => 'gp_productreview', 'slug' => 'product-review', 'title' => 'Product Review'), 
            array('id' => 'gp_advertorial', 'slug' => 'eco-friendly-products', 'title' => 'Products'),
	        array('id' => 'gp_projects', 'slug' => 'projects', 'title' => 'Projects'),
	        array('id' => 'gp_greengurus', 'slug' => 'green-gurus', 'title' => 'Green Gurus')
        );
	    
	    self::$posttypes = $posttypes;
	    
	    /** Serviced Editions  **/
	    $editions = array(
	        array('iso2' => 'AU', 'name' => 'Australia'), 
	        array('iso2' => 'GB', 'name' => 'Great Britain'), 
	        array('iso2' => 'NZ', 'name' => 'New Zealand'), 
	        array('iso2' => 'IE', 'name' => 'Ireland'), 
	        array('iso2' => 'IN', 'name' => 'India'), 
	        array('iso2' => 'FR', 'name' => 'France'), 
	        array('iso2' => 'CA', 'name' => 'Canada'), 
	        array('iso2' => 'US', 'name' => 'United States of America')
	    );
	     
	    self::$editions = $editions;
	}
	
	public static function getPostTypes() {
	    self::init();
	    return self::$posttypes;
	}
	
	public static function getEditions() {
	    self::init();
	    return self::$editions;
	}
	
}
?>