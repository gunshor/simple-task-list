<?php

class RESTDispatcher
{

	private $_aControllers  = array();
	private $_oDbConnection;

	/**
	 * Go-go gadget constructor!
	 *
	 * @param DbConnection $oDbConnection
	 */
	public function __construct( DbConnection $oDbConnection )
	{
		$this->_oDbConnection = $oDbConnection;
	}

	/**
	 * registers the $sElement to the controller class $sClassName
	 *
	 * @param unknown_type $sElement
	 * @param unknown_type $sClassName
	 */
	public function registerController( $sElement, $sClassName )
	{
		$this->_aControllers[ $sElement ] = $sClassName;
	}

	/**
	 * Dispatches the correct controller for the specified element.
	 *
	 * @todo this is obviously very fragile, but it's good enough for this code
	 * 	test
	 *
	 * @param string $sRequestMethod
	 * @param string $sElement
	 * @param int $iId
	 */
	public function dispatch( $sRequestMethod, $sElement = null, $iId = null )
	{
		if ( !array_key_exists( $sElement, $this->_aControllers ) ) {
			throw new Exception(
				'No controller registered to the specified element.' );
		}

		require_once  $this->_aControllers[ $sElement ] . '.php';
		$oController = new $this->_aControllers[ $sElement ];

		$oController->setDbConnection( $this->_oDbConnection );

		$oController->preDispatch();
		switch( strtoupper( $sRequestMethod ) ) {

			case 'DELETE':
				call_user_func( array( $oController, 'delete'), $iId );
				break;

			case 'POST':
				call_user_func( array( $oController, 'save'), $iId );
				break;

			case 'GET':
				call_user_func( array( $oController, 'load'), $iId );
				break;

			default:
				throw new Exception(
					'unsupported request method: ' . $sRequestMethod );
		}
	}

}