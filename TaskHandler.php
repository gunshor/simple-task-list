<?php
require_once 'Task.php';

/**
 * @todo
 *
 */
class TaskHandler
{
	/**
	 * @var DbConnection
	 */
	private $_oConnection;

	private $_sTableName  = 'tasks';
	private $_sPrimaryKey = 'id';

	public function __construct( DbConnection $oDbConnection )
	{
		$this->_oConnection = $oDbConnection;
	}

	/**
	 * Saves the task to the database.  If the task was a new task, it will be
	 * updated with the corresponding primary key.
	 *
	 * @param Task $oTask explicitly passed by reference in order to update
	 * 		primary key (and possibly other fields in the future).
	 * @return boolean true on success
	 */
	public function save( Task &$oTask )
	{
		if ( is_null( $oTask->getId() ) ) {

			$iResult = $this->_oConnection->insert( $this->_sTableName, $oTask->toArray() );
			if( false !== $iResult ) {
				$oTask->setId( $iResult );
			}

		} else {
			$iResult = $this->_oConnection->update(
				$this->_sTableName,
				$oTask->toArray(),
				array( $this->_sPrimaryKey => $oTask->getId() )
			);
		}

		return true;
	}

	/**
	 * Deletes a task from the database
	 *
	 * @todo this could be used to update the priority as well if we wanted to
	 * 	get really tricky.
	 *
	 * @param 	int$iTaskId
	 * @return 	bool was the deletion successful?
	 */
	public function delete( $iTaskId )
	{
		$iResult = $this->_oConnection->delete(
			$this->_sTableName,
			array( $this->_sPrimaryKey => $iTaskId )
		);

		return $iResult > 0;
	}

	/**
	 * Returns one {@link Task} with the specified Id.
	 *
	 * @param int $iTaskId
	 * @return Task or null if the specified task couldn't be found
	 */
	public function find( $iTaskId )
	{
		$aWhere = array( $this->_sPrimaryKey => intval( $iTaskId ) );
		$aResult = $this->_oConnection->select(
			$this->_sTableName, array(), $aWhere
		);

		return array_shift( $aResult );
	}

	/**
	 * Loads tasks from the database
	 *
	 * @param array $aConditions conditions to limit the query
	 */
	public function load( array $aConditions = array(), array $aOrder = array() )
	{
		$aResult = $this->_oConnection->select(
			$this->_sTableName, array(), $aConditions, $aOrder
		);

		$aTasks = array();
		foreach( $aResult as $iNum => $aData ) {
			$oTask = new Task();
			$oTask->setId( $aData[ 'id' ] );
			$oTask->setDescription( $aData[ 'description' ] );
			$oTask->setPriority( $aData[ 'priority' ] );
			$oTask->setStatus( $aData[ 'status' ] );
			$oTask->setUpdatedAt( $aData[ 'updated' ] );
			$aTasks[$iNum] = $oTask->toArray();
		}

		return $aTasks;
	}
}