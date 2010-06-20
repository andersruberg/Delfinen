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
 * Provides a rendere using the zend view.
 * 
 * @category	Spiffy
 * @package		Spiffy_Calendar
 * @copyright  	Copyright (c) 2009 Kyle Spraggs. http://www.spiffyjr.me
 * @license 	http://www.spiffyjr.me/license	New BSD License 
 */
class Spiffy_Calendar_Render_ZendView extends Spiffy_Calendar_Render_Abstract implements 
	Spiffy_Calendar_Render_Interface
{
	protected $_template = null;
	
	public function init()
	{
		$this->setTemplate('calendar.phtml');
	}
	
	public function setTemplate($templateName)
	{
		$this->_template = $templateName;
		return $this;
	}
	
	public function getTemplate()
	{
		return $this->_template;
	}
	
	public function render()
	{
		if (!$templateName = $this->getTemplate()) {
			return '';
		}
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = clone $viewRenderer->view;
		$view->clearVars();
		$view->calendar = $this->getCalendar();
		$view->baseUrl = Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
		$view->addScriptPath(APPLICATION_PATH . '/views/layouts/');
		
		return $view->render($templateName);
	}
}