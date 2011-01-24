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
 * Abstract class for event sources.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
abstract class Spiffy_Calendar_Event_Source_Abstract
{
	// Events for this source
	protected $_events = array();
	
	// Default day, month, and year
	protected $_day = null;
	protected $_month = null;
	protected $_year = null;
	
	// Date
	protected $_data;
	
	// Additional parameters
	protected $_params;
	
	public function __construct($data = null, $params = null)
	{
		// Set our parameters
		if (isset($params['defaultDate'])) {
			$this->setDate($params['defaultDate']);
		}
		
		$this->setData($data);
		$this->setParams($params);
		
		// Children initialization done here
		$this->init();
	}
	
	public function init()
	{}
	
	public function getData()
	{
		return $this->_data;
	}
	
	public function setData($data)
	{
		$this->_data = $data;
		return $this;
	}
	
	public function getDay()
	{
		return $this->_day;
	}
	
	public function setDay($day)
	{
		$this->_day = $day;
		return $this;
	}
	
	public function getMonth()
	{
		return $this->_month;
	}
	
	public function setMonth($month)
	{
		$this->_month = $month;
		return $this;
	}
	
	public function getYear()
	{
		return $this->_year;
	}
	
	public function setYear($year)
	{
		$this->_year = $year;
		return $this;
	}
	
	public function getDate()
	{
		return array('day' => $this->getDay(), 'month' => $this->getMonth(), 'year' => $this->getYear());
	}
	
	// TODO: Add checks for proper date format
	public function setDate($date)
	{
		$this->setDay($date['day']);
		$this->setMonth($date['month']);
		$this->setYear($date['year']);
		return $this;
	}
	
	public function getParam($name)
	{
		if (isset($this->_params[$name])) {
			return $this->_params[$name];
		}
		
		return null;
	}
	
	public function getParams()
	{
		return $this->_params;
	}
	
	public function setParams($params)
	{
		$this->_params = $params;
		return $this;
	}
	
	public function getEvents()
	{
		if (0 == count($this->_events)) {
			$this->populateEvents();
		}
		return $this->_events;
	}
}