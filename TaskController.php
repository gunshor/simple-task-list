<?php
require_once 'RESTController.php';
require_once 'TaskHandler.php';

class TaskController extends RESTController
{
	/**
	 * @var TaskHandler
	 */
	private $_oTaskHandler;

	/**
	 * Go-go gadget constructor!
	 *
	 */
	public function __construct()
	{
		parent::__construct();

	}

	public function preDispatch()
	{
		$this->_oTaskHandler = new TaskHandler( $this->_oDbConnection );
	}

	public function save()
	{
		// TODO passing $_POST data directly in isn't safe. Of course, or lower
		// level abstractions prevent injection, so this can be refactored later.
		$aTaskData = $_POST;

		if ( !empty( $aTaskData ) ) {

			$oTask = new Task();
			$oTask->setId( !empty( $aTaskData['id'] ) ? $aTaskData['id'] : null );
			$oTask->setDescription( urldecode( $aTaskData['description'] ) );
			$oTask->setStatus( $aTaskData['status'] );
			$oTask->setPriority( (int) $aTaskData['priority'] );

			$this->_oTaskHandler->save( $oTask );
		}

		echo json_encode( array(
			'result' => self::SUCCESS,
			'task'	 => $oTask->toArray(),
		) );
	}

	public function delete( $iTaskId )
	{
		try {
			$this->_oTaskHandler->delete( $iTaskId );
		} catch ( Exception $e ) {
			$this->_throwException( $e );
		}

		echo json_encode( array(
			'result' => self::SUCCESS,
		) );
	}

	public function load( $iTaskId = null )
	{
		try {
			if ( !empty( $iTaskId ) ) {
				$aTasks = array( $this->_oTaskHandler->find( $iTaskId ) );
			} else {
				$aTasks = $this->_oTaskHandler->load(
					array(), array( 'priority' => 'ASC' ));
			}
		} catch ( Exception $e ) {
			$this->_throwException( $e );
		}

		echo json_encode( array(
			'result' => self::SUCCESS,
			'tasks'  => $aTasks
		) );
	}


}