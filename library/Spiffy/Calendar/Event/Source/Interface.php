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
 * Interface for event sources.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
interface Spiffy_Calendar_Event_Source_Interface
{
	/**
	 * Populates the events
	 */
	public function populateEvents();
}