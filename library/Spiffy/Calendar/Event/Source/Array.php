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
 * Event data source with an array as the backend.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
class Spiffy_Calendar_Event_Source_Array extends Spiffy_Calendar_Event_Source_Abstract implements 
	Spiffy_Calendar_Event_Source_Interface
{
	public function populateEvents()
	{
		if (null === $this->getData() || 0 == count($this->getData())) {
			throw new Zend_Exception('Data empty for Spiffy_Calendar_Event_Array');
		}
		
		$events = array();
		
		// For each array of data in $this->_data add a new event.
		foreach ($this->getData() as $data) {
			// Set the default day/month/year if one doesn't exist in the parameters
			$eventDate = $data['eventDate'];
			
			if (!is_array($eventDate)) {
				$day = $this->getDay();
				$month = $this->getMonth();
				$year = $this->getYear();
			} else {
				if (isset($eventDate['day'])) {
					$day = $eventDate['day'];
				}
				if (isset($eventDate['month'])) {
					$month = $eventDate['month'];
				}
				if (isset($eventDate['year'])) {
					$year = $eventDate['year'];
                               
				}
			}
			
			$events[] = new Spiffy_Calendar_Event($data, $this->getParams(), 
				array('day' => $day, 'month' => $month, 'year' => $year));
		}
		
		$this->_events = $events;
	}
}