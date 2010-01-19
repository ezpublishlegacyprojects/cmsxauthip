<?php

/**
 * cmsxAuthIPTools Auth IP Utilities
 * 
 */
class cmsxAuthIPTools
{
    /**
     * Constructor
     */
    public function __construct(){}
    public static function cleanAddress( $addr )
    {
    	$addr = explode( '.', $addr );
    	if ( isset( $addr[0], $addr[1] ) )
    	{
	    	$addr[0] = str_replace( '*', '0', $addr[0] );
    		$addr[1] = str_replace( '*', '0', $addr[1] );    		
    	}
    	$addr = implode( '.', $addr );
        $addr = preg_replace( "/([^\d|\-|*|.]*)/", '', trim( $addr ) );
        $addr = preg_replace( "/(((^|\.|-)0+)(\d+))/", '$3$4', $addr );
        $addr = preg_replace( "/(\d{3})(\d+)/", '$1', $addr );
        return $addr;
    }
    public static function cleanWildcard( $addr, $start = true )
    {
    	if ( $start )
    	{
    		return str_replace( '*', '0', $addr );
    	}
    	$pat = array( '/(\.|^|-)\*/', '/(\.|^|-)(\d{1})\*/', '/(\.|^|-)(\d{2})\*/'  );
		$rep = array( '${1}255', '${1}${2}55', '${1}${2}5' );    	
    	$clean = preg_replace( $pat, $rep, $addr );
        return $clean;
    }    
    public static function address2long( $addr )
    {
    	return floatval( sprintf( "%u", ip2long( $addr ) ) );
    }
    public static function isValidIP( $ip )
    {
    	return ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4|FILTER_FLAG_NO_RES_RANGE ) );
    }
    public static function isValidIPRange( $from, $to )
    {
        if ( !self::isValidIP( $from ) || !self::isValidIP( $to ) )
        {
        	return false;
        }
        $from = self::address2long( $from );
        $to = self::address2long( $to );
        return ( $from < $to );
    }    
    public static function validateAddress( $addr )
    {
    	$addr = self::cleanAddress( $addr );
    	$validation = array();
    	$validation['is_valid'] = false;
    	$validation['error'] = false;
    	$validation['type'] = cmsxAuthIP::IP_ADDRESS;
    	$validation['content'] = $addr;
	    if ( ( strpos( $addr, '-' ) !==  false ) || ( strpos( $addr, '*' ) !== false ) )
	    {
	    	if ( strpos( $addr , '-' ) !==  false )
	    	{
	    		list( $from, $to ) = explode( '-', $addr, 2 ) ;
	    	}
	    	else
	    	{
	    		$validation['content'] = $addr = preg_replace( '/(\*)(\d+)(\.|$|-)/', '$1$3', $addr );
	    		$from = self::cleanAddress( self::cleanWildcard( $addr ) );
	            $to = self::cleanAddress( self::cleanWildcard( $addr, false ) );
	    	}
	    	$validation['range_start'] = $from;
    	    $validation['range_end'] = $to;
	    	$validation['type'] = cmsxAuthIP::IP_RANGE;
	        $validation['is_valid'] = $isValid = ( self::isValidIPRange( $from, $to ) && strpos( $from, '*' ) === false && strpos( $to, '*' ) === false );
	        if ( !$isValid )
	        {
	        	$validation['error'] = ezi18n( 'extension/cmsxauthip/edit', 'This range IP address is not valid' );
	        }
	        return $validation;
	    }    	
	    $validation['is_valid'] = $isValid = self::isValidIP( $addr );
        if ( !$isValid )
        {
        	$validation['error'] = ezi18n( 'extension/cmsxauthip/edit', 'This IP address is not valid' );
        }
	    return $validation;
    }
}

?>