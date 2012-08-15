<?php
$client = new SoapClient 
( 
	"http://localhost:8080/ws/services/AssetOperationService?wsdl", 
	array ('trace' => 1 ) 
);	
$auth = array ('username' => 'admin', 'password' => 'admin' );

$identifier = array 
(
	'path' => array(path => '/my-xml-block'),
	'type' => 'block'
);

$readParams = array ('authentication' => $auth, 'identifier' => $identifier);
$reply = $client->readAccessRights($readParams);

if ($reply->readAccessRightsReturn->success=='true')
{
	$accessRightsInformation = $reply->readAccessRightsReturn->accessRightsInformation;
	$aclEntries = $accessRightsInformation->aclEntries->aclEntry;
                
    if (!is_array($aclEntries)) // For less than 2 eleements, the returned object isn't an array
		$aclEntries=array($aclEntries);
	
	$aclEntries[] = array('level' => 'read', 'type' => 'user', 'name' => 'admin');
	$accessRightsInformation->aclEntries->aclEntry=$aclEntries;

	$editParams = array
	(
		'authentication' => $auth, 
		'accessRightsInformation' => $accessRightsInformation, 
		'applyToChildren' => false 
	);
    $reply = $client->editAccessRights($editParams);
    if ($reply->editAccessRightsReturn->success=='true')		
		echo "Success.";
	else
		echo "Error occurred when editing access rights: " . $reply->editAccessRightsReturn->message;
}
else
	echo "Error occurred: " . $reply->readAccessRightsReturn->message;
?>