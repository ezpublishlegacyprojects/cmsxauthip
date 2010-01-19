<?php

$module = $Params['Module'];
// user
$userID = $Params['UserID'] ? $Params['UserID'] : false;
// id
$rowID = $Params['IPID'] ? $Params['IPID'] : false;

// view parameters   
$viewParameters = array();
if( $userID )
{
    $viewParameters['user'] = $userID;
}
if( $rowID )
{
    $viewParameters['id'] = $rowID ;
}
//init properties
$address = new stdClass();
$address->id = $rowID;
$address->user_id = $userID;
$address->content = '';
$address->range_start = 0;
$address->range_end = 0;
$address->type = cmsxAuthIP::IP_ADDRESS;
$address->status = true;
$address->is_saved = false;
$address->used = false;

$ezhttp = eZHTTPTool::instance();

if( $ezhttp->hasPostVariable( 'LastView' ) )
{
	$LastView = $ezhttp->postVariable( 'LastView' );
}
else
{
	$LastView = $ezhttp->sessionVariable( "LastAccessedModifyingURI" );
}

// content/browse for user
if( $module->isCurrentAction( 'BrowseForUser' ) )
{
    $postActionParamList = $module->Functions['edit']['post_action_parameters']['SaveAddress'];

    $persistentDataArray = array();
    $persistentDataArray['HasObjectInput'] = 0;

    foreach( $postActionParamList as $postActionParam )
    {
        $value = null;

        if( $ezhttp->hasPostVariable( $postActionParam ) )
        {
            $value = $ezhttp->postVariable( $postActionParam );
        }
        $persistentDataArray[$postActionParam] = $value;
    }
    $fromAddressEditPagePrefix = 'authip/edit';

	if( $userID )
	{
	  $fromAddressEditPagePrefix .= '/(user)/' . $userID;
	}
	if( $rowID )
	{
	  $fromAddressEditPagePrefix .= '/(id)/' . $rowID ;
	}

    eZContentBrowse::browse( array( 'action_name' => 'BrowseForAuthIpUser',
                                    'persistent_data' => $persistentDataArray,
                                    'from_page'       => $fromAddressEditPagePrefix,
                                    'cancel_page'     => $fromAddressEditPagePrefix ),
                                    $module );
    return;
}


// error messages
$errorMessages = array();

// check if mode is new or edit
$editMode = false;
if( $address->id )
{
	$editAddress = cmsxAuthIP::fetch( $address->id );
	if ( is_object( $editAddress ) )
	{
		$editMode = true;
		$address->id = $editAddress->attribute( 'id' );
		$address->content = $editAddress->attribute( 'address' );
		$address->user_id = $editAddress->attribute( 'user_id' );
		$address->status = ( $editAddress->attribute( 'status' ) == cmsxAuthIP::STATUS_ACTIVE );
		$address->type = $editAddress->attribute( 'type' );
		if ( $address->type == cmsxAuthIP::IP_RANGE )
		{
			$address->range_start = long2ip( $editAddress->attribute( 'range_start' ) );
		    $address->range_end = long2ip( $editAddress->attribute( 'range_end' ) );
		}
	}
}

// get post variables
if( $ezhttp->hasPostVariable( 'AddressID' ) )
{
	$address->id = $ezhttp->postVariable( 'AddressID' );
}
if( $ezhttp->hasPostVariable( 'AddressUserID' ) )
{
	$address->user_id = $ezhttp->postVariable( 'AddressUserID' );
}
if( $ezhttp->hasPostVariable( 'Address' ) )
{
	$address->content = $ezhttp->postVariable( 'Address' );
}
if( $ezhttp->hasPostVariable( 'AddressStatus' ) )
{
	$address->status = $ezhttp->postVariable( 'AddressStatus' );
}

// get user from 
if ( $ezhttp->hasPostVariable( 'BrowseActionName' ) &&
     $ezhttp->postVariable( 'BrowseActionName' ) == 'BrowseForAuthIpUser' &&
     $ezhttp->hasPostVariable( 'SelectedObjectIDArray' ) )
{
	$resultUser = eZContentBrowse::result( 'BrowseForAuthIpUser' );
	if ( isset( $resultUser[0] ) )
	{
		$address->user_id = $resultUser[0];
	}
}

// get user object
if( $address->user_id )
{
	$User = eZUser::fetch( $address->user_id );
	if ( !is_object( $User ) )
	{
    	$module->redirectTo( $LastView );
    	return;
	}
}
else
{
    $User = eZUser::currentUser();
    $address->user_id = $User->attribute( 'contentobject_id' );
}

if( $module->isCurrentAction( 'SaveAddress' ) )
{
    if( $module->hasActionParameter( 'Address' ) )
    {
		$NewAddress = cmsxAuthIPTools::validateAddress( $module->actionParameter( 'Address' ) );
		$address->content = $NewAddress['content'];
        $address->status = ( $module->actionParameter( 'AddressStatus' ) == cmsxAuthIP::STATUS_ACTIVE );
        $address->type = $NewAddress['type'];
		if ( $address->type == cmsxAuthIP::IP_RANGE )
		{
			$address->range_start = $NewAddress['range_start'] ;
			$address->range_end = $NewAddress['range_end'];
		}
		if ( $NewAddress['is_valid'] )
		{
			if ( $address->type == cmsxAuthIP::IP_RANGE )
			{
				$usedByUser = cmsxAuthIP::findByRange( $address->range_start, $address->range_end, $address->user_id, true, $address->id );
			    if ( $usedByUser )
			    {
			    	$errorMessages[] = ezi18n( 'extension/cmsxauthip/edit', 'This user has another address that uses this range' );
			    }
				$usedByOthers = cmsxAuthIP::findByRange( $address->range_start, $address->range_end, $address->user_id, false, $address->id  );
				if ( $usedByOthers )
			    {
			    	$errorMessages[] = ezi18n( 'extension/cmsxauthip/edit', 'This IP range is used by another user' );
			    }
			}
			else
			{
				$usedByUser = cmsxAuthIP::findByIP( $address->content, $address->user_id, true, $address->id );
				if ( $usedByUser )
			    {
			    	$errorMessages[] = ezi18n( 'extension/cmsxauthip/edit', 'This user has another address with this value' );
			    }
			    $usedByOthers = cmsxAuthIP::findByIP( $address->content, $address->user_id, false, $address->id );
				if ( $usedByOthers )
			    {
			    	$errorMessages[] = ezi18n( 'extension/cmsxauthip/edit', 'This IP address is used by another user' );
			    }
			}
			if ( !$usedByUser && !$usedByOthers )
			{
				$StoreAddress = array( 'user_id' => $address->user_id,
				                       'address' => $address->content,
				                       'type' => $address->type,
				                       'range_start' => $address->range_start,
				                       'range_end' => $address->range_end,
				                       'status' => $address->status
				                     );
				$AddressSaved = cmsxAuthIP::storeIP( $StoreAddress, $address->id );
				if ( $AddressSaved && is_numeric( $AddressSaved->attribute( 'id' ) ) )
				{
					$address->id = $AddressSaved->attribute( 'id' );
					$address->is_saved = true;
				}
			}
			else
			{
				$address->used = array_merge( $usedByUser, $usedByOthers );
			}
		}
		else
		{
			$errorMessages[] = $NewAddress['error'];
		}
    }
}


include_once( 'kernel/common/template.php' );

$tpl = templateInit();
$tpl->setVariable( 'address', get_object_vars( $address ) );
$tpl->setVariable( 'user', $User->contentObject() );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'errorMessages', $errorMessages );
$tpl->setVariable( 'last_view', $LastView );

$Result = array();
$Result ['content'] = $tpl->fetch( 'design:authip/edit.tpl' );
$Result['path'] = array( array( 'url' => false, 
                                'text' =>  ezi18n( 'extension/cmsxauthip', 'IP Authentication' ) ),
                         array( 'url' => false,
                                'text' => ezi18n( 'extension/cmsxauthip', 'Edit Address' )  ) );

?>