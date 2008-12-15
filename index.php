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

$aControllers = array(
	'task' => 'TaskController'
);


if ( !empty( $sElement ) ) {

	$oController = new $aControllers[ $sElement ];

	switch ( strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {

		case 'GET':




	}