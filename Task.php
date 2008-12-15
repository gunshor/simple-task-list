<?php

/**
 * This class is essentially a Value Object for tasks.
 *
 * @author A.J. Brown <aj@ajbrown.org>
 */
class Task
{
	/**
	 * The unique id for this task
	 * @internal should match the autonum in the databse
	 * @var int
	 */
	private $_iId;

	/**
	 * The ordered priority of the task.
	 * @var int
	 */
	private $_iPriority;

	/**
	 * A description of the task
	 * @var string
	 */
	private $_sDescription;

	/**
	 * @var string
	 */
	private $_sStatus;

	/**
	 * The mysql timestamp when this item was last updated
	 * @var string
	 */
	private $_sUpdatedAt;

	/**
	 * Getter for the status of this task
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->_sStatus;
	}

	public function setStatus( $sStatus )
	{
		$this->_sStatus = $sStatus;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return isset( $this->_iId ) ? $this->_iId : null;
	}

	/**
	 * @param 	int
	 * @return  void
	 */
	public function setId( $iId )
	{
		$this->_iId = $iId;
	}

	/**
	 * @return int
	 */
	public function getPriority()
	{
		return $this->_iPriority;
	}

	/**
	 * @param int $iPriority
	 */
	public function setPriority( $iPriority )
	{
		$this->_iPriority = $iPriority;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->_sDescription;
	}

	/**
	 * @param string $sDescription
	 */
	public function setDescription( $sDescription )
	{
		$this->_sDescription = $sDescription;
	}

	public function getUpdatedAt()
	{
		 return $this->_sUpdatedAt;
	}

	public function setUpdatedAt( $sMySqlDate )
	{
		 $this->_sUpdatedAt = $sMySqlDate;
	}

	public function toArray()
	{
		return array(
			'id' 		  => $this->getId(),
			'priority'    => $this->getPriority(),
			'description' => $this->getDescription(),
			'status'      => $this->getStatus(),
			'updated'	  => $this->getUpdatedAt(),
		);
	}
}