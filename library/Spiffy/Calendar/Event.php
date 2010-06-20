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
 * Container for an event object.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
class Spiffy_Calendar_Event
{
	protected $_data;
	protected $_day = null;
	protected $_month = null;
	protected $_year = null;
	protected $_params;
	
	public function __construct($data = array(), $params = array(), $date = array())
	{
		$this->setData($data);
		$this->setDate($date);
		$this->setParams($params);
	}
	
	public function getData($name = null)
	{
		if (null === $name) {
			return $this->_data;
		} else {
			return $this->_data[$name];
		}
	}
	
	public function setData(array $data)
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
	
	public function setParam($name, $value)
	{
		$this->_params[$name] = $value;
		return $this;
	}
	
	public function setParams(array $params)
	{
		$this->_params = $params;
		return $this;
	}
	
	public function getParam($name)
	{
		return isset($this->_params[$name]) ? $this->_params[$name] : null;
	}
	
	public function getParams()
	{
		return $this->_params;
	}
}