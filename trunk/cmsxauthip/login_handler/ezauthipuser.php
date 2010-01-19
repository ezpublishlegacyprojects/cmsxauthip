<?php
/**
 * File containing the AuthIPUser class
 * @package cmsxauthip
 */

class eZAuthIPUser extends eZUser
{
    public function __construct( $row = null )
    {
        @parent::eZUser( $row );
    }

    /**
     * Logs in the user if applied login and password is valid.
     *
     * @param string $login
     * @param string $password
     * @param bool $authenticationMatch
     * @return mixed eZUser or false
     */
    public static function loginUser( $login, $password, $authenticationMatch = false )
    {
    	$ip = array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $ip = preg_replace( '/( |\,)/', '', $ip );
        if( $password == '' )
        {
            $auth = cmsxAuthIP::findByIP( $ip, false, false, false, true  );
            if( !isset( $auth[0] ) )
            {
            	return false;
            }
            $ini = eZINI::instance( 'authip.ini' );
            // match user is enabled?
        	$matchUsername = ( $ini->variable( 'AuthIPSettings', 'LoginHandlerMatchUser' ) == 'enabled' );
        	$userID = $auth[0]['user_id'];
        	$address = $auth[0]['address'];        	
            $user = eZUser::fetch( $userID );
            // match user part 2
            $matchUsername = $matchUsername ? ( $user->attribute( 'login' ) == $login ) : true;
            if ( is_object( $user ) && $user->isEnabled() && $matchUsername )
            {
            	self::loginSucceeded( $user );
            	// audit login
            	eZAudit::writeAudit( 'ip-login', array( 'User id' => $userID, 
        		                                        'User login' => $user->attribute( 'login' ),
        		        		                        'Address rule' => $address ) );
            	return $user;
            }
        }
        return false;
    }
}

?>
