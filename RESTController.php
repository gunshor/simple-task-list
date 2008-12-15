<?php

abstract class RESTController
{
	const SUCCESS = 'SUCCESS';
	const FAILURE = 'FAILURE';

	/**
	 * @var DbConnection
	 */
	protected $_oDbConnection;

	/**
	 * Go-go gadget constructor!
	 *
	 */
	public function __construct()
	{}

	public function setDbConnection( DbConnection $oDb )
	{
		$this->_oDbConnection = $oDb;
	}

	public function getRawPostData()
	{
		return trim( file_get_contents('php://input') );
	}

	/**
	 * Throw exceptions by changing the response status to 500.  I.E. ignores
	 * custom error messages somethings, so we have to ensure the response is
	 * at least 1024 chars. Otherwise, our AJAX can't read the exception message
	 * back to the user.
	 *
	 * @param Exception $e
	 * @return void
	 */
	protected function _throwException( Exception $e )
	{
		header('HTTP/1.1 500 Internal Server Error', 500 );
		die( str_pad( $e->getMessage(), 1024, ' ', STR_PAD_RIGHT ) );
	}

	public abstract function preDispatch();
	public abstract function save();
	public abstract function delete( $iId );
	public abstract function load( $iId = null );
}