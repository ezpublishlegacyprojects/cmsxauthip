<?php

$Module = $Params['Module'];

$offset = $Params['Offset'];
if( !is_numeric( $offset ) )
{
    $offset = 0;
}
// view parameters   
$viewParameters = array();
if( $offset )
{
    $viewParameters['offset'] = $offset ;
}

$currentUser = eZUser::currentUser();

$currentUserID = $currentUser->attribute( 'contentobject_id' );

require_once( "kernel/common/template.php" );

if( $Module->isCurrentAction( 'ActivateIP' ) )
{
    if( $Module->hasActionParameter( 'IPsIDArray' ) )
    {
        foreach( $Module->actionParameter( 'IPsIDArray' ) as $ipsID )
        {
            cmsxAuthIP::swapStatus( $ipsID );
        }
    }
}

if( $Module->isCurrentAction( 'DeleteIP' ) )
{
    if( $Module->hasActionParameter( 'IPsIDArray' ) )
    {
        foreach( $Module->actionParameter( 'IPsIDArray' ) as $ipsID )
        {
            cmsxAuthIP::removeIP( $ipsID );
        }
    }
}
if( $Module->isCurrentAction( 'NewIP' ) )
{
    $Module->redirectToView( 'edit' );
}


$tpl = templateInit();
$tpl->setVariable( 'current_user', $currentUser->contentObject() );
$tpl->setVariable( 'addresses', cmsxAuthIP::fetchByUserId( $currentUserID, $offset ) );
$tpl->setVariable( 'addresses_count', cmsxAuthIP::countByUserId( $currentUserID ) );
$tpl->setVariable( 'view_parameters', $viewParameters );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:authip/view.tpl' );
$Result['path'] = array( array( 'url' => false, 
                                'text' =>  ezi18n( 'extension/cmsxauthip', 'IP Authentication' ) ),
                         array( 'url' => false,
                                'text' => ezi18n( 'extension/cmsxauthip', 'User addresses' )  ) );
?>