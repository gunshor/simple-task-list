<?php

require_once ( 'DbConnection.php' );

class SqLiteConnection extends DbConnection
{
	const ESC_CHAR = '';

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
		$aRequiredParams = array( 'database' );
		$aMissingParams  = array_diff( $aRequiredParams, array_keys( $aParams ) );
		if ( count( $aMissingParams ) > 0 ) {
			throw new Exception(
				'Expected parameter not found: ' . $aMissingParams
			);
		}

		$this->_oPDO = new PDO( 'sqlite:' . $aParams[ 'database' ] );
	}
}