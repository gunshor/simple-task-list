<?php

/**
 * Super class for all of our database connection classes.
 *
 * Since SQLLite and MySQL do queries the same way, this class works for it's
 * duties. This wouldn't be an acceptable design for a larger application that
 * wishes to stay database agnostic though. In the real-world, we'd use
 * Doctrine (preferred), Propel, or Zend_Db_Table.
 *
 * @author A.J. Brown <aj@ajbrown.org>
 */
abstract class DbConnection
{
	/**
	 * The character to use for escaping identifiers.  Implementing classes
	 * should redefine this specifically for their database platform.
	 *
	 * @var string
	 */
	protected $_sEscChar	= '';

	public $_aQueries    = array();

	/**
	 * @var PDO
	 */
	protected $_oPDO;

	/**
	 * @var int
	 */
	private $_iAffectedRows;

	/**
	 * @var int
	 */
	private $_iLastInsertId;

	/**
	 * Opens the connection to the database, and sets the _oPDP object to a
	 * new PDO.
	 *
	 * @param array $aParams
	 */
	public abstract function connect( array $aParams );

	/**
	 * Performs a query that doesn't return results.  Implementing classes
	 * may throw an exception on failure instead of returning false.
	 *
	 * @param string $sQuery
	 * @return boolean true if the query succeeded, false if not
	 */
	public function exec( $sQuery )
	{
		$this->_aQueries[] = $sQuery;

		$mResult = $this->_oPDO->exec( $sQuery );
		if ( $mResult !== false ) {
			$this->_iAffectedRows = $mResult;
			return true;
		}

		return false;
	}

	/**
	 * Gets the number of affected rows from the last DELETE or UPDATE
	 * query.
	 *
	 * @return int number of affected rows
	 */
	public function getAffectedRows()
	{
		return $this->_iAffectedRows;
	}

	/**
	 * Gets the primary key from the last insert query
	 *
	 * @return int
	 * @see DbConnection::getLastInsertId()
	 */
	public function getLastInsertId()
	{
		return $this->_oPDO->lastInsertId();
	}

	/**
	 * Performs a query that should return results.  Implementing classes
	 * may throw an exception on failure instead of returning false.
	 *
	 * @param string $sQuery
	 * @return array resulting rows as objects;
	 */
	public function query( $sQuery )
	{
		$this->_aQueries[] = $sQuery;
		$mResult = $this->_oPDO->query( $sQuery );

		if ( !$mResult ) die( var_export( $this->_aQueries ) );

		return $mResult->fetchAll( PDO::FETCH_ASSOC );
	}

	/**
	 * Performs and insert query
	 *
	 * @param string $sInto
	 * @param array $aData
	 * @return int the last insert id on success, false on failure.
	 */
	public function insert( $sInto, array $aData )
	{
		$sTemplate = 'INSERT INTO %s (%s) VALUES (%s)';

		$sInto = $this->_escapeIdentifier( $sInto );

		// generate fields and values for query
		list( $aFields, $aValues ) = $this->_extractFieldsAndValues( $aData );
		$sFields = join( ',', $aFields );
		$sValues = join( ',', $aValues );

		// perform query
		$sSql = sprintf( $sTemplate, $sInto, $sFields, $sValues );
		$bResult = $this->exec( $sSql );

		return $bResult ? $this->getLastInsertId() : false;
	}

	/**
	 * Performs and update query on records which match there where parameters.
	 *
	 * @param string $sTable the table to update
	 * @param array  $aData
	 * @param array  $aWhere
	 * @return int the number of affected rows, or false if the query failed.
	 */
	public function update( $sTable, array $aData, array $aWhere = array() )
	{
		$sTemplate = 'UPDATE %s SET %s WHERE %s';

		$aData  = $this->_escapeDataSet( $aData );
		if( count( $aWhere ) > 0 ) {
			$aWhere = $this->_escapeDataSet( $aWhere );
		} else {
			$aWhere = array( 1 => 1 );
		}

		$sTable = $this->_escapeIdentifier( $sTable );

		// don't be fooled by dumb function names, this works perfectly.  Why
		// the php team didn't create a function like this that doesn't call
		// itself `http`, I don't know.
		$sSet   = $this->_toNameValueString( $aData,  ', ' );
		$sWhere = $this->_toNameValueString( $aWhere, ', ' );

		$sSql = sprintf( $sTemplate, $sTable, $sSet, $sWhere );
		return $this->exec( $sSql ) ? $this->getAffectedRows() : false;
	}

	/**
	 * Performs and delete on rows which match there where parameters.
	 *
	 * @param string $sTable the table to update
	 * @param array  $aWhere
	 * @return int the number of affected rows, or false if the query failed.
	 */
	public function delete( $sTable, array $aWhere = array() )
	{
		$sTemplate = 'DELETE FROM %s WHERE %s';

		if( count( $aWhere ) > 0 ) {
			$aWhere = $this->_escapeDataSet( $aWhere );
		} else {
			$aWhere = array( 1 => 1 );
		}

		$sWhere = http_build_query( $aWhere, '', ' AND ' );

		$sSql = sprintf( $sTemplate, $sTable,$sWhere );
		return $this->exec( $sSql ) ? $this->getAffectedRows() : false;
	}

	/**
	 * Performs a select query with the proper parameters.
	 *
	 * @todo this knowingly does not support LIMIT and WHERE clause expressions
	 * 	are limited to boolean AND.  Again, it works fine for the needs of the test
	 * 	but isn't ideal for a real application.
	 *
	 * @internal queries without order by aren't very optimal.  Doing
	 * 	ORDER BY 1 DESC forces a filesort.  But, again, this is just for demo :)
	 *
	 * @param string $sFrom
	 * @param array  $aWhere format: array ( '<fieldname>' => '<equals value> );
	 * @param array  $aOrderBy format: array( '<fieldname>' => ASC|DESC );
	 *
	 * @return array(Track) the tracks matching the query
	 */
	public function select( $sFrom, array $aFields = array(),
		array $aWhere = array(), array $aOrderBy = array() )
	{
		$sTemplate = 'SELECT %s FROM %s WHERE %s ORDER BY %s';

		if ( count( $aFields ) > 0 ) {
			$aFields = array_walk( $aFields, array( &$this, '_escapeIdentifier' ) );
		}

		// build array for WHERE
		if ( count( $aWhere ) > 0 ) {
			$aWhere  = $this->_escapeDataSet( $aWhere );
		} else {
			// if the where clause is empty, WHERE 1=1
			$aWhere = array( 1 => 1 );
		}

		// build array for ORDER BY
		$aOrderString = array();
		if( count( $aOrderBy ) > 0 ) {
			foreach ( $aOrderBy as $sField => $sDir ) {
				$aOrderString[] = $this->_escapeIdentifier( $sField ) . ' ' . $sDir;
			}
		} else {
			$aOrderString[] = '1';
		}

		$sFields = count( $aFields ) > 0 ? join( ', ', $aFields ) : '*';
		$sFrom	 = $this->_escapeIdentifier( $sFrom );
		$sWhere  = join( ' AND ', $aWhere );
		$sOrder  = join( ', ', $aOrderString );

		$sSql = sprintf( $sTemplate, $sFields, $sFrom, $sWhere, $sOrder );

		return  $this->query( $sSql );
	}

	public function getQueryLog()
	{
		return $this->_aQueries;
	}



	/**
	 * Escapes an identier for use in a query.
	 * @param 	string	$sIdentifier
	 * @return 	string	the escaped $sIdentifier
	 */
	protected function _escapeIdentifier( $sIdentifier )
	{
		return $this->_sEscChar . $sIdentifier . $this->_sEscChar;
	}

	/**
	 * Escapes a field value for a query.  Type-aware, so php types will
	 * be formated properly for it's equivalent database type.
	 *
	 * @internal I hope MySQL decides to support a true boolean type like
	 * 		PostgreSQL one day.
	 *
	 * @param mixed $sValue
	 */
	protected function _escapeValue( $mValue )
	{
		if( is_int( $mValue ) || is_float( $mValue ) ) {
			return $mValue;
		} elseif ( is_bool( $mValue ) ) {
			return $mValue ? 1 : 0;
		} elseif ( is_null( $mValue ) ) {
			return 'NULL';
		} else {
			return $this->_oPDO->quote( $mValue );
		}
	}

	/**
	 * transforms a key => values array into one array of escaped fields, and one
	 * array of values.  Useful for INSERT statements
	 *
	 * @param  array $aDataSet the name-value pair dataset to extract from
	 * @return array [0] = fields, [1] = values
	 */
	protected final function _extractFieldsAndValues( array $aDataSet )
	{
		$aReturn = array();
		// extract and escape the fields and values from the data
		foreach( $aDataSet as $mKey => $mValue ) {
			$aReturn[0][] = $this->_escapeIdentifier( $mKey );
			$aReturn[1][] = $this->_escapeValue( $mValue );
		}

		return $aReturn;
	}

	/**
	 * Escapes the key and value of a dataset.
	 * @param $aDataSet
	 * @return array the dataset with both keys and values escaped.
	 *
	 */
	protected final function _escapeDataSet( array $aDataSet )
	{
		list( $aKeys, $aValues ) = $this->_extractFieldsAndValues( $aDataSet);
		return array_combine( $aKeys, $aValues );
	}

	/**
	 * Combines an array into a naame-value string.  This is similar to
	 * http_build_query(), except that no additional escaping is done.
	 *
	 * @param array $aDataSet
	 */
	protected final function _toNameValueString( array $aDataSet, $sSeperator = ', ')
	{
		$aReturn = array();
		foreach( $aDataSet as $mKey => $mValue ) {
			$aReturn[] = "{$mKey} = {$mValue}";
		}
		return join( $sSeperator, $aReturn );
	}

}