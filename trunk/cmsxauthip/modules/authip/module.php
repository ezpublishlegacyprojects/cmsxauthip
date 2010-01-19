<?php
$Module = array( 'name' => 'CMSX Auth IP',
                 'functions' => array( 'administrate', 'use' ) );

$ViewList = array();


$ViewList['view'] = array(
    'script'                  => 'view.php',
    'default_navigation_part' => 'ezauthipnavigationpart',
    'ui_context'              => 'administration',
    'functions' => array( 'administrate', 'use' ),
    'single_post_actions'     => array( 'NewIpButton'   => 'NewIP',
                                        'ActivateIpButton'   => 'ActivateIP',
                                        'DeleteIpButton' => 'DeleteIP' ),

    'post_action_parameters'  => array( 'ActivateIP'   => array( 'IPsIDArray' => 'IPsIDArray' ),
                                        'DeleteIP' => array( 'IPsIDArray' => 'IPsIDArray' ) ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$ViewList['administrate'] = array(
    'script'                  => 'administrate.php',
    'default_navigation_part' => 'ezauthipnavigationpart',
    'ui_context'              => 'administration',
    'functions' => array( 'administrate' ),
    'single_post_actions'     => array( 'NewIpButton'   => 'NewIP',
                                        'ActivateIpButton'   => 'ActivateIP',
                                        'DeleteIpButton' => 'DeleteIP' ),

    'post_action_parameters'  => array( 'ActivateIP'   => array( 'IPsIDArray' => 'IPsIDArray' ),
                                        'DeleteIP' => array( 'IPsIDArray' => 'IPsIDArray' ) ),
    'unordered_params' => array( 'offset' => 'Offset' )  );


$ViewList['edit'] = array(
    'script'                  => 'edit.php',
    'default_navigation_part' => 'ezauthipnavigationpart',
    'ui_context'              => 'administration',
    'functions' => array( 'administrate', 'use' ),
    'single_post_actions'     => array( 'SaveAddressButton' => 'SaveAddress',
                                        'BrowseForUserButton' => 'BrowseForUser' ),

    'post_action_parameters'  => array( 'SaveAddress' => array( 'Address'        => 'Address',
                                                                'AddressID'  => 'AddressID',
                                                                'AddressStatus'  => 'AddressStatus',
                                                                'AddressUserID'  => 'AddressUserID',
                                                                'LastView'  => 'LastView'),

                                        'BrowseForUser'  => array() ),

    'post_actions'            => array( 'BrowseActionName' ),

    'unordered_params' => array( 'user'     => 'UserID',
                                 'id' => 'IPID' ) );

$FunctionList = array();
$FunctionList['administrate'] = array();
$FunctionList['use'] = array();

?>
