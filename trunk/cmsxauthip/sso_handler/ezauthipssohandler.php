<?php


class eZAuthIPSSOHandler
{
	function handleSSOLogin()
    {
        $http = eZHTTPTool::instance();
        $now = time() ;
        $newCheck = false;
        $ini = eZINI::instance( 'authip.ini' );
        $timer = (int) $ini->variable( 'AuthIPSettings', 'SingleSignOnTimer' );
        if ( $http->hasSessionVariable( 'LastIpCheck' ) && $now >= $http->sessionVariable( 'LastIpCheck' ) )
		{
    		$newCheck = true;
		}
        elseif( !$http->hasSessionVariable( 'LastIpCheck' ) )
        {
        	$http->setSessionVariable( 'LastIpCheck', $now + ( $timer * 60 ) );
        	$newCheck = true;
        }
        if ( $newCheck )
        {
           	$ip = array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        	$ip = preg_replace( '/( |\,)/', '', $ip );
			$auth = cmsxAuthIP::findByIP( $ip, false, false, false, true );
        	if( !isset( $auth[0] ) )
        	{
        		$http->setSessionVariable( 'LastIpCheck', $now + ( $timer * 60 ) );
        		return false;
        	}
        	$userID = $auth[0]['user_id'];
        	$address = $auth[0]['address'];
        	$user = eZUser::fetch( $userID );
        	if ( is_object( $user ) && $user->isEnabled() )
        	{
        		$http->removeSessionVariable( 'LastIpCheck' );
        		// audit login
        		eZAudit::writeAudit( 'ip-login', array( 'User id' => $userID, 
        		                                        'User login' => $user->attribute( 'login' ),
        		        		                        'Address rule' => $address ) );
        		return $user;
        	}
        	else
        	{
        		$http->setSessionVariable( 'LastIpCheck', $now + ( $timer * 60 ) );
        	}        	
        }
        return false;
    }
}

?>
