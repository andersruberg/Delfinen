<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CalendarController extends My_Controller_Action
{
    protected $_gcal;

    public function init()
    {
        

        
    }

    public function preDispatch()
    {
        
        $this->_gcal = new Model_GCalendar();
       
    }

    public function indexAction()
    {
        $this->_helper->actionStack('list', 'calendar');
        $this->_helper->actionStack('thumbnails', 'photo');
        $this->_helper->actionStack('list', 'blog');

        /*$events = $this->_gcal->getEvents();
        //TODO: Integrate Google Calendar with Spiffy Calendar
        if (count($events) != 0) {
            $_cal = $this->_cal->createEvent('array', $events, array('dateFormat' => 'YYYY-MM-DD'));
        }
        
        $id = $this->_getParam('eventid');
        if ($id != null) {
            $this->view->event = $this->_gcal->getEventById($id);
        }

        $this->view->calendar = $this->_cal;*/
         
    }

    public function listAction()
    {
        $this->_helper->viewRenderer->setResponseSegment('events');
        
        $this->view->events = $this->_gcal->getNextEvents();
        
    }




}
