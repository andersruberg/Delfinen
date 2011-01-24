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
 * The primary calendar generation class.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
class Spiffy_Calendar
{
	// Days of week as indexed by Zend_Date
	const SUNDAY = 0;
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	
	// Date of the calendar
	protected $_date = null;
	
	// Keeps an array of events
	protected $_events = array();
	
	// Start of the week
	protected $_startDay = self::MONDAY;
	
	// Days of the calendar
	protected $_days = array();
	
	// Weeks of the month
	protected $_weeks = array();
	
	/**
	 * Calendar constructor initializes cache if one is available
	 * from the registry under the key 'Zend_Calendar_Cache'
	 * @access public
	 * @param array params
	 */
	public function __construct($date = null)
	{
		$this->setDate($date);
		//		parent::__construct($date, $part, $locale);
		//		
		//		// Use the Zend_Calendar_Cache if registered
		//		if (Zend_Registry::isRegistered('Zend_Calendar_Cache')) {
		//			$cache = Zend_Registry::get('Zend_Calendar_Cache');
		//			if (!($cache instanceof Zend_Cache_Core)) {
		//				throw new Spiffy_Calendar_Exception('Spiffy_Calendar requires an instance of Zend_Cache_Core');
		//			}
		//			$this->setOptions(array('cache' => Zend_Registry::get('Zend_Calendar_Cache')));
		//		}
		

		$this->init();
	}
	
	/**
	 * Serialize as string
	 *
	 * Proxies to {@link render()}.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		try {
			$return = $this->render();
			return $return;
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
		}
		
		return '';
	}
	
	public function init()
	{}
	
	/**
	 * Wrapper for this->getDate()->get()
	 */
	public function get($format)
	{
		return $this->getDate()->get($format);
	}
	
	/**
	 * Adds multiple events from an array to their
	 * proper day in the calendar.
	 * @param array events to add
	 */
	public function addEvents($events)
	{
		$now = Zend_Date::now();
		foreach ($events as $event) {
			$day = $now->get(Zend_Date::DAY_SHORT);
			$month = $now->get(Zend_Date::MONTH_SHORT);
			$year = $now->get(Zend_Date::YEAR);
			
			// Get the day/month/year of the event
			if (null !== $event->getDay()) {
				$day = $event->getDay();
			}
			if (null !== $event->getMonth()) {
				$month = $event->getMonth();
			}
			if (null !== $event->getYear()) {
				$year = $event->getYear();
			}
			
			$this->getDay($day, $month, $year)->addEvent($event);
		}
	}
	
	/**
	 * Create an event for the calendar using the 
	 * specified event type.
	 *
	 * @param variable string type of event OR event object
	 * @param variable data
	 * @param array params
	 * @return Spiffy_Calendar
	 */
	public function createEvent($source, $data = null, array $params = array())
	{
		if (is_string($source)) {
			$source = 'Spiffy_Calendar_Event_Source_' . ucfirst($source);
			
			Zend_Loader::loadClass($source);
			
			// Initialize the event class and pass by reference the Spiffy_Calendar
			$source = new $source($data, $params);
			
			$this->addEvents($source->getEvents());
		} else if ($source instanceof Spiffy_Calendar_Event_Source_Abstract) {
			$this->addEvents($source->getEvents());
		} else {
			throw new Spiffy_Calendar_Exception('Invalid event type');
		}
		
		return $this;
	}
	
	/**
	 * Render block
	 *
	 * @return string
	 */
	public function render($adapterName = null)
	{
		return Spiffy_Calendar_Render::factory($this, $adapterName)->render();
	}
	
	public function getDate()
	{
		return $this->_date;
	}
	
	public function setDate($date = null)
	{
		$now = Zend_Date::now();
		if (is_array($date)) {
			if (isset($date['day']) === true) {
				$now->setDay($date['day']);
			}
			if (isset($date['month']) === true) {
				$now->setDay($date['month']);
			}
			if (isset($date['year']) === true) {
				$now->setDay($date['year']);
			}
			
			$date = $now;
		} else if (!$date instanceof Zend_Date) {
			$date = $now;
		}
		
		$this->_date = $date;
	}
	
	public function getDays()
	{
		return $this->_days;
	}

        public function getMonthDigit()
        {
            $date = new Zend_Date($this->_date);
            return $date->getMonth()->toString("M");
        }

        public function getNextMonth($format)
        {
            $date = new Zend_Date($this->_date);
            return $date->addMonth(1)->get($format);
        }

        public function getPrevMonth($format)
        {
            $date = new Zend_Date($this->_date);
            return $date->subMonth(1)->get($format);
        }

        public function getPrevYear($type = null)
        {
            $date = new Zend_Date($this->_date);
            return $date->subYear(1)->toString("Y");
        }

        public function getNextYear($type = null)
        {
            $date = new Zend_Date($this->_date);
            return $date->addYear(1)->toString("Y");
        }

        public function getYearDigit()
        {
            $date = new Zend_Date($this->_date);
            return $date->getYear()->toString("Y");
        }

        public function getYearList()
        {

            return array("2010", "2011", "2012");
        }
	
	public function getDay($day, $month, $year)
	{
		if (!isset($this->_days[$day][$month][$year])) {
			$newDay = new Spiffy_Calendar_Day();
			$newDay->setDay($day)->setMonth($month)->setYear($year);
			
			$this->_days[$day][$month][$year] = $newDay;
		}
		
		return $this->_days[$day][$month][$year];
	}
	
	public function getEvents($day = null, $month = null, $year = null)
	{
		if (null !== $year) {
			return $this->_events[$day][$month][$year];
		}
		if (null !== $month) {
			return $this->_events[$day][$month];
		}
		if (null !== $day) {
			return $this->_events[$day];
		}
		return $this->_events;
	}
	
	public function getDayNames()
	{
		$dayNames = Zend_Locale::getTranslationList('day', $this->getDate()->getLocale());
		
		// Shift based on startDay
		$start = $this->getStartDay();
		while ($start--) {
			$tmp = array_shift($dayNames);
			$dayNames[] = $tmp;
		}
		
		return $dayNames;
	}
	
	public function getMonthNames()
	{
		if (0 == count($this->_monthNames)) {
			$this->_monthNames = Zend_Locale::getTranslationList('Month', $this->_locale);
		}
		
		return $this->_monthNames;
	}


	
	public function getEndDay()
	{
		if ($this->getStartDay() == self::MONDAY) {
			return self::SUNDAY;
		} else {
			return self::SATURDAY;
		}
	}
	
	public function getStartDay()
	{
		if (null === $this->_startDay) {
			$this->setStartDay();
		}
		
		return $this->_startDay;
	}
	
	public function setStartDay($start = self::SUNDAY)
	{
		$this->_startDay = $start;
	}
	
	public function getWeeks()
	{
		$date = new Zend_Date(
			array('month' => $this->get(Zend_Date::MONTH), 'day' => 1, 'year' => $this->get(Zend_Date::YEAR)));
		
		$daysInMonth = $date->get(Zend_Date::MONTH_DAYS);
		
		// Get the first day based on the starting day of the month
		$firstDay = $date->get(Zend_Date::WEEKDAY_DIGIT);
		
		// Shift the calendar to position the first day appropriate with user settings
		if ($firstDay < $this->getStartDay()) {
			$date->subDay(7 - ($this->getStartDay() - $firstDay));
		} else {
			$date->addDay($this->getStartDay() - $firstDay);
		}
		
		$tempDays = $firstDay + $daysInMonth;
		$weeksInMonth = ceil($tempDays / 7);
		
		$weeks = array();
		for ($j = 0; $j < $weeksInMonth; $j++) {
			for ($i = 0; $i < 7; $i++) {
				$weeks[$j][$i] = $this->getDay($date->get(Zend_Date::DAY_SHORT), $date->get(Zend_Date::MONTH_SHORT), 
					$date->get(Zend_Date::YEAR));
				$date->addDay(1);
			}
		}
		
		return $weeks;
	}
}