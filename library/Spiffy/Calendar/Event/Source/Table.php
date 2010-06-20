<?php
/**
 * Spiffy Library
 *
 * LICENSE
 * 
 * This source file is subject to the new BSD license that is
 * available through the world-wide-web at this URL:
 * http://www.spiffyjr.me/license
 * 
 * @category   	Spiffy
 * @package    	Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License
 * @author 		Kyle Spraggs <theman@spiffyjr.me>
 */

/**
 * Event data source with a Zend_Db_Table as the backend.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
class Spiffy_Calendar_Event_Source_Table extends Spiffy_Calendar_Event_Source_Abstract implements 
	Spiffy_Calendar_Event_Source_Interface
{
	protected $_select = null;
	
	public function populateEvents()
	{
		if (!$this->getTable() instanceof Zend_Db_Table_Abstract) {
			throw new Spiffy_Calendar_Exception(
				'Spiffy_Calendar_Event_Table requires an instance of Zend_Db_Table_Abstract');
		}
		
		$resultSet = $this->getData()->fetchAll($this->getSelect());
		
		$events = array();
		foreach ($resultSet as $row) {
			$dateField = $this->getParam('dateField');
			$dateFormat = $this->getParam('dateFormat');
			
			// Preset the date with the defaultDate paramater if it exists in case the table date is invalid
			$day = $this->getDay();
			$month = $this->getMonth();
			$year = $this->getYear();
			
			// Check if date field and format are set and, if so, use those for the event date			
			if (null !== $dateField && null !== $dateFormat) {
				$date = $row->{$dateField};
				
				// Verify a valid date exists
				if (Zend_Date::isDate($row->{$dateField}, $dateFormat)) {
					$date = new Zend_Date($row->{$dateField});
					
					// Reset the day/month/year to be based on the table field
					$day = $date->get(Zend_Date::DAY_SHORT);
					$month = $date->get(Zend_Date::MONTH_SHORT);
					$year = $date->get(Zend_Date::YEAR);
				}
			}
			
			$events[] = new Spiffy_Calendar_Event($row->toArray(), $this->getParams(), 
				array('day' => $day, 'month' => $month, 'year' => $year));
		}
		
		$this->_events = $events;
	}
	
	public function setTable($table)
	{
		$this->setData($table);
	}
	
	public function getTable()
	{
		return $this->getData();
	}
	
	public function getSelect()
	{
		if (null === $this->_select) {
			$this->setSelect();
		}
		
		return $this->_select;
	}
	
	public function setSelect($select = null)
	{
		if (null === $select) {
			$this->_select = $this->getData()->select();
		} else {
			if (!$select instanceof Zend_Db_Table_Select) {
				throw new Spiffy_Calendar_Exception(
					'Spiffy_Calendar_Event_Table::setSelect() requires an instance of Zend_Db_Table_Select');
			}
			$this->_select = $select;
		}
	}
}