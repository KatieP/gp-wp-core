<?php 

class GeoSession {
    
    static protected $shared = array();
    protected $parameters = array();
    
    public function __construct( array $parameters = array() ) {
        $this->parameters = $parameters;
    }
    
    public function getStates() {
        if ( isset( self::$shared['states'] ) ) {
            return self::$shared['states'];
        }
        global $gp;
        $ns_loc = $gp->location['country_iso2'] . '\\Edition';
        return self::$shared['states'] = $ns_loc::getStates();
    }
    
}

?>