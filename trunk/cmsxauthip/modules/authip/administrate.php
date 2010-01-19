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
$tpl->setVariable( 'addresses', cmsxAuthIP::fetchAll( $offset ) );
$tpl->setVariable( 'addresses_count', cmsxAuthIP::countAll() );
$tpl->setVariable( 'view_parameters', $viewParameters );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:authip/administrate.tpl' );
$Result['path'] = array( array( 'url' => false, 
                                'text' =>  ezi18n( 'extension/cmsxauthip', 'IP Authentication' ) ),
                         array( 'url' => false,
                                'text' => ezi18n( 'extension/cmsxauthip', 'Authentication addresses' )  ) );
?>