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
 * Class to provide a renderer for the calendar.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
final class Spiffy_Calendar_Render
{
	const DEFAULT_ADAPTER = 'ZendView';
	
	public static function factory(Spiffy_Calendar $grid, $adapterName = null)
	{
		if (null === $adapterName) {
			$adapterName = self::DEFAULT_ADAPTER;
		}
		
		if (!is_string($adapterName) or !strlen($adapterName)) {
			throw new Exception('Reder Calendar: Adapter name must be specified in a string.');
		}
		
		$adapterName = 'Spiffy_Calendar_Render_' . $adapterName;
		
		Zend_Loader::loadClass($adapterName);
		return new $adapterName($grid);
	}
}
