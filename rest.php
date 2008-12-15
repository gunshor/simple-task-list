<?php
/**
 * A simple RESTful dispatcher.  Calls the correct Controller and method
 * for a request type and element id.
 *
 * This was meant to implment a REST interface, but it was decided
 * that using .htaccess might get in the way of running the demo.
 *
 * So, instead, we use the following URI schema:
 *
 * REST: http://exampl.org/task/1
 * HERE: http://exampl.org/index.php?element=task&id=1
 *
 */

require_once 'config.php';
require_once 'MySqlConnection.php';
require_once 'SqLiteConnection.php';

//---------------------------------------
// Open the correct DB Connection
//---------------------------------------
if ( $aConfig[ 'DbPlatform' ] == 'mysql' ) {

	$aParams = array(
		'host'     => $aConfig[ 'DbHostname' ],
		'username' => $aConfig[ 'DbUsername' ],
		'password' => $aConfig[ 'DbPassword' ],
		'database' => $aConfig[ 'DbSchema' ],
	);

	$oDbConnection = new MySqlConnection();
} elseif ( $aConfig[ 'DbPlatform' ] == 'sqlite' ) {

	$aParams = array(
		'database'     => $aConfig[ 'DbSchema' ],
	);

	$oDbConnection = new SqLiteConnection();
}

$oDbConnection->connect( $aParams );

//---------------------------------------
// Dispatch the request correctly
//---------------------------------------
$sElement = isset( $_GET[ 'element' ] ) ? $_GET[ 'element' ] : null;
$iElementId = isset( $_GET[ 'id' ] ) ? $_GET[ 'id' ] : null;

require_once 'RESTDispatcher.php';
$oDispatcher = new RESTDispatcher( $oDbConnection );

$oDispatcher->registerController( 'task', 'TaskController' );
$oDispatcher->dispatch( $_SERVER['REQUEST_METHOD'], $sElement, $iElementId );

//log the queries that are run
file_put_contents( 'dbqueries.log',
	join( "\n", $oDbConnection->_aQueries ) . PHP_EOL, FILE_APPEND );