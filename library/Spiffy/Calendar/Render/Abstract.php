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
 * Renderer abstract.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
class Spiffy_Calendar_Render_Abstract
{
	protected $_calendar = null;
	
	public function __construct(Spiffy_Calendar $calendar)
	{
		$this->setCalendar($calendar);
		$this->init();
	}
	
	public function init()
	{}
	
	public function setCalendar(Spiffy_Calendar $calendar)
	{
		$this->_calendar = $calendar;
		return $this;
	}
	
	public function getCalendar()
	{
		return $this->_calendar;
	}
}