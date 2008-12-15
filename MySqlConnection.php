<?php

require_once ( 'DbConnection.php' );

class MySqlConnection extends DbConnection
{
	/**
	 * @see DbConnection::_sEscChar
	 * @var string
	 */
	protected $_sEscChar	= '`';

	/**
	 * @param array $aParams
	 * @see DbConnection::connect()
	 * @return void
	 */
	public function connect( array $aParams )
	{
		//-------------------------------------------------
		// make sure we have all of the required parameters
		//-------------------------------------------------
		$aRequiredParams = array( 'host', 'username', 'password', 'database' );
		$aMissingParams  = array_diff( $aRequiredParams, array_keys( $aParams ) );
		if ( count( $aMissingParams ) > 0 ) {
			throw new Exception(
				'Expected parameter not found: ' . join( ', ',$aMissingParams )
			);
		}

		$this->_oPDO = new PDO(
			"mysql:dbname={$aParams[ 'database' ]};host={$aParams[ 'host' ]}",
			$aParams[ 'username' ], $aParams[ 'password' ]
		);
	}
}