<?php

function cmsxauthip_ContentActionHandler( $Module, $http, $objectID )
{
    if ( $http->hasPostVariable( 'ViewUserIPs' ) )
    {
        return $Module->redirectTo( '/authip/view/(user)/' . $objectID  );
    }

    return false;
}

?>
