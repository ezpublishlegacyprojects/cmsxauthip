<?php

/**
 * cmsxAuthIP persistent object class implementation
 * 
 */
class cmsxAuthIP extends eZPersistentObject
{
	const IP_ADDRESS = 1;
	const IP_RANGE = 2;
	
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
	
    /**
     * Constructor
     * 
     * @param array $row
     */
    public function __construct( $row = array() )
    {
        parent::__construct( $row );
    }

    /**
     * Field list definition for persistent object
     * 
     * @return array
     */
    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'user_id' => array( 'name' => 'UserID',
                                                             'datatype' => 'integer',
                                                             'default' => '0',
                                                             'required' => true,
                                                             'foreign_class' => 'eZUser',
                                                             'foreign_attribute' => 'contentobject_id',
                                                             'multiplicity' => '1..*' ),
                                         'address' => array( 'name' => 'Address',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'range_start' => array( 'name' => 'RangeStart',
                                                          'datatype' => 'float',
                                                          'default' => '0',
                                                          'required' => false ),
                                         'range_end' => array( 'name' => 'RangeEnd',
                                                          'datatype' => 'float',
                                                          'default' => '0',
                                                          'required' => false ),                
                                         'type' => array( 'name' => 'Type',
                                                             'datatype' => 'integer',
                                                             'default' => '1',
                                                             'required' => true ),
                                         'status' => array( 'name' => "Status",
                                                              'datatype' => 'integer',
                                                              'default' => '1',
                                                              'required' => false ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array(  ),
                      'increment_key' => 'id',
                      'class_name' => __CLASS__,
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'cmsxauthip' );
    }

    /**
     * Fetches cmsxAuthIP object by given ID
     * 
     * @param integer $id
     * @return cmsxAuthIP
     */
    public static function fetch( $id )
    {
    	$row = self::fetchObject( self::definition(), 
                                                   null, 
                                                   array( 'id' => $id ) );
        return $row;
    }
    
    /**
     * Fetch by user
     * 
     * @return cmsxAuthIP|null
     */
    public static function fetchByUserId( $user_id, $offset = 0 )
    {   
        $ips = self::fetchObjectList( self::definition(), null, 
                                                        array( 'user_id' => $user_id ), null,
                                                        array( 'offset' => $offset, 'limit' => self::getLimit() ) );
        return $ips;
    }
    public static function fetchAll( $offset = 0 )
    {	
        return eZPersistentObject::fetchObjectList( self::definition(), null, null, null, 
                                                    array( 'offset' => $offset, 'limit' => self::getLimit() ) );
    }
    public static function getLimit()
    {	
    	$limitVal = array( 1 => 10, 2 => 25, 3 => 50 );
    	$limit = eZPreferences::value( 'authip_limit' );
    	return ( $limit ) ? $limitVal[min( $limit, 3 )] : 10;
    }
    public static function countByUserId( $user_id )
    {
    	return eZPersistentObject::count( self::definition(), array( 'user_id' => $user_id )  );
    }
    public static function countAll()
    {	
        return eZPersistentObject::count( self::definition() );
    }    
    public static function storeIP( $addr, $rowId = false )
    {	
    	$Address = $rowId ? self::fetch( $rowId ) : new self( array( 'id' => null ) );
    	$oldAddress = clone $Address;
    	$isNew = ( $rowId == false );
		if ( is_object( $Address ) )
		{
		    $Address->setAttribute( 'address', $addr['address'] );
		    $userID = isset( $addr['user_id'] ) ? $addr['user_id'] : eZUser::currentUser()->attribute( 'contentobject_id' );
		    $Address->setAttribute( 'user_id', $userID );
		    // type and range address
		    $newType = self::IP_ADDRESS;
		    $newRangeStart = 0;
		    $newRangeEnd = 0;
		    if( $addr['type'] == self::IP_RANGE )
		    {
		    	$newType = self::IP_RANGE;
		    	$newRangeStart = cmsxAuthIPTools::address2long( $addr['range_start'] );
		        $newRangeEnd = cmsxAuthIPTools::address2long( $addr['range_end'] );
		    }

		    $Address->setAttribute( 'type', $newType );		    
		    $Address->setAttribute( 'range_start', $newRangeStart );
		    $Address->setAttribute( 'range_end', $newRangeEnd );
		    // status
		    $newStatus = ( $addr['status'] == self::STATUS_ACTIVE ) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE; 
		    $Address->setAttribute( 'status', $newStatus  );
	        $db = eZDB::instance();
	        $db->begin();
	        $Address->store();
	        $db->commit();
	        if ( !is_object( $Address ) )
	        {
	        	return false;
	        }
	        $status = ( $newStatus == self::STATUS_ACTIVE ) ? 'enabled' : 'disabled';	
        	$audit = array( 'Address ID' => $Address->attribute( 'id' ), 
		                    'Address' => $Address->attribute( 'address' ),
		                    'User ID' => $Address->attribute( 'user_id' ),
		                    'Status' => $status );
		    if ( $isNew )
        	{
       	    	// audit change
			    eZAudit::writeAudit( 'ip-change', array_merge( $audit, array( 'Operation' => 'NEW' ) ) );     
        	}
        	else
        	{
        		$changes = self::checkChanges( $oldAddress, $Address );
        		if ( $changes )
        		{
        			// audit change
			    	eZAudit::writeAudit( 'ip-change', array_merge( $audit, array( 'Changes' => $changes, 'Operation' => 'EDIT' ) ) );  
        		}
        	}   		  		
	        return $Address;
		} 
		return false;
    }
    public static function checkChanges( $oldAddr, $newAddr )
    {
        $changes = '';
        if ( $oldAddr->attribute( 'address' ) != $newAddr->attribute( 'address' ) )
        {
        	$changes .= 'address ' . $oldAddr->attribute( 'address' ) . ' to ' . $newAddr->attribute( 'address' ) . ', ';
        }
        if ( $oldAddr->attribute( 'user_id' ) != $newAddr->attribute( 'user_id' ) )
        {
        	$changes .= 'user_id ' . $oldAddr->attribute( 'user_id' ) . ' to ' . $newAddr->attribute( 'user_id' ) . ', ';
        }
        if ( $oldAddr->attribute( 'status' ) != $newAddr->attribute( 'status' ) )
        {
        	$oldStatus = ( $oldAddr->attribute( 'status' ) == self::STATUS_ACTIVE ) ? 'enabled' : 'disabled';
        	$newStatus = ( $newAddr->attribute( 'status' ) == self::STATUS_ACTIVE ) ? 'enabled' : 'disabled';
        	$changes .= 'status ' . $oldStatus . ' to ' . $newStatus . ', ';
        }
        if ( $changes == '' )
        {
        	return false;
        }
        return preg_replace( '/, $/', '', $changes );
    }
    public static function swapStatus( $id )
    {
    	$Address = self::fetch( $id );
    	if ( is_object( $Address ) )
    	{
    		$db = eZDB::instance();
            $db->begin();
            $oldStatus = ( $Address->attribute( 'status' ) == self::STATUS_ACTIVE ) ? 'enabled' : 'disabled';	
    		$newStatus = ( $Address->attribute( 'status' ) == self::STATUS_ACTIVE ) ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;
    		$Address->setAttribute( 'status', $newStatus );
    		$Address->store();
    		$db->commit();
    		if ( is_object( $Address ) )
    		{
    			$status = ( $newStatus == self::STATUS_ACTIVE ) ? 'enabled' : 'disabled';	
       	    	// audit change
			    eZAudit::writeAudit( 'ip-change', array( 'Address ID' => $Address->attribute( 'id' ), 
		                                                 'Address' => $Address->attribute( 'address' ),
		                                                 'User ID' => $Address->attribute( 'user_id' ),
		                                                 'Status' => $status,
        		                                         'Changes' => 'status ' . $oldStatus . ' to ' . $status,
        		                                         'Operation' => 'SWAP-STATUS' ) );     
    		}
    	}
    }
    public static function removeIP( $id )
    {
    	$Address = self::fetch( $id );
        eZPersistentObject::removeObject( self::definition(),
                                          array( 'id' => $id ) );
        $status = ( $Address->attribute( 'status' ) == self::STATUS_ACTIVE ) ? 'enabled' : 'inactive';	
       	// audit change
		eZAudit::writeAudit( 'ip-change', array( 'Address ID' => $Address->attribute( 'id' ), 
		                                         'Address' => $Address->attribute( 'address' ),
		                                         'User ID' => $Address->attribute( 'user_id' ),
		                                         'Status' => $status,
        		                                 'Operation' => 'DELETE' ) );                                     
    }
	public static function findByIP( $ip, $userId = false, $matchUser = false, $exclude = false, $onlyActive = false )
	{
		$db = eZDB::instance();
		$rangeType = self::IP_RANGE;
		$ipType = self::IP_ADDRESS;
		$match = $matchUser ? '=' : '!=';
		$user = $userId ? " AND user_id $match '$userId' " : '';
		$exclude = $exclude ? " AND id NOT IN('$exclude') " : '';
		$onlyActive = $onlyActive ? " AND status = " . self::STATUS_ACTIVE : '';
		$ipLong = cmsxAuthIPTools::address2long( $ip );
	    $sql = "SELECT DISTINCT *
                    FROM cmsxauthip
                        WHERE ( address = '$ip' AND type = $ipType $user $exclude $onlyActive  ) OR
                              ( range_start <= $ipLong AND range_end >= $ipLong AND type = $rangeType $user $exclude $onlyActive )
				             ORDER BY user_id ASC;";
     	$result = $db->arrayQuery( $sql, array(), false );
      	return $result;
	}
	public static function findByRange( $start, $end, $userId = false, $matchUser = false, $exclude = false, $onlyActive = false  )
	{
		$db = eZDB::instance();
		$rangeType = self::IP_RANGE;
		$ipType = self::IP_ADDRESS;
		$match = $matchUser ? '=' : '!=';
		$user = $userId ? " AND user_id $match '$userId' " : '';
		$exclude = $exclude ? " AND id NOT IN('$exclude') " : '';
		$onlyActive = $onlyActive ? " AND status = " . self::STATUS_ACTIVE : '';
		$startLong = cmsxAuthIPTools::address2long( $start );
		$endLong = cmsxAuthIPTools::address2long( $end );		
	    $sql = "SELECT DISTINCT *
                    FROM cmsxauthip
                        WHERE ( address BETWEEN '$start' AND '$end' AND type = $ipType $user $exclude $onlyActive  ) OR
                              ( range_start BETWEEN $startLong AND $endLong AND type = $rangeType $user $exclude $onlyActive  ) OR
                              ( range_end BETWEEN $startLong AND $endLong AND type = $rangeType $user $exclude $onlyActive )
				             ORDER BY user_id ASC;";
     	$result = $db->arrayQuery( $sql, array(), false );
      	return $result;
	}	
}

?>