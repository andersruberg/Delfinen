<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../library/Google/GData.php');

class Google_GCalendar extends Google_GData
{
    
    protected $holidaysURI = 'http://www.google.com/calendar/feeds/sv.swedish%23holiday@group.v.calendar.google.com/public/basic';
    protected $calendarURI = 'http://www.google.com/calendar/feeds/dykklubben.delfinen@gmail.com/public/basic';
    
    /*
     * Google Calender without authentication
     */
    

     public function __construct($user = 'default') {

        if ($user == 'default') //Authenticated session
            parent::__construct(Zend_Gdata_Calendar::AUTH_SERVICE_NAME);

        $this->_user = $user;
        Zend_Loader::loadClass('Zend_Gdata_Calendar');
        $this->_service = new Zend_Gdata_Calendar($this->_client);


    }

    public function isAuthenticated()
    {
        if (isset ($this->_client))
            return true;
        else
            return false;
    }

    public function getService()
    {
        return $this->_service;
    }


    public function getCalendars()
    {
        return $this->_service->getCalendarListFeed();
    }

    public function getPublicEvents($user = null, $orderBy = 'starttime', $sortOrder='ascending', $futureEvents = 'true', $fullText = '')
    {
        $query = $this->_service->newEventQuery();


        $query->setVisibility('public');
        $query->setUser($user);
        $query->setProjection('full');

        $query->setQuery($fullText);
        $query->setOrderby($orderBy);
        $query->setFutureevents($futureEvents);
        $query->setSortOrder($sortOrder);
        // Retrieve the event list from the calendar server

        try {
            return $this->_service->getCalendarEventFeed($query);
        }
        catch (Zend_Gdata_App_Exception $e) {
            return "Error: " . $e->getResponse();
        }
    }

    public function getEvents($query = null)
    {
        

        try {
            return $this->_service->getCalendarEventFeed($query);
        }
        catch (Zend_Gdata_App_Exception $e) {
            return "Error: " . $e->getResponse();
        }
    }


    
    public function getPrivateEvents()
    {
        $query = $this->_service->newEventQuery();
        
        
        $query->setVisibility('private');
        $query->setUser('default');
        
        $query->setProjection('full');
        $query->setOrderby('starttime');
        $query->setFutureevents('true');
        $query->setSortOrder('ascending');
        // Retrieve the event list from the calendar server
        
        var_dump($query->getQueryUrl());

        try {
            return $this->_service->getCalendarEventFeed($query);
        }
        catch (Zend_Gdata_App_Exception $e) {
            return "Error: " . $e->getResponse();
        }
    }

    public function getEventById($eventId)
    {
        $query = $this->_service->newEventQuery();
        $query->setUser('default');
        $query->setVisibility('private');
        $query->setSingleEvents('true');
        #$query->setRecurrenceExpansionStart('2010-01-01');
        #$query->setRecurrenceExpansionEnd('2010-12-31');
        $query->setEvent($eventId);
        
        // Retrieve the event list from the calendar server
        //TODO: Add support for recurrent events
        try {
            $event = $this->_service->getCalendarEventEntry($query);

            $ID = $event->id->text;
            $title = $event->title->text;
            $content = $event->content->text;
            
            foreach($event->when as $when) {
                $startTime =  $when->startTime;
                $endTime =  $when->endTime;
            }
            $event = array('id'=> $ID, 'title'=>$title, 'content' => $content, 'startTime'=> $startTime, 'endTime'=>$endTime);
         
            return $event;
        }
        catch (Zend_Gdata_App_Exception $e) {
            return "Error: " . $e->getResponse();
        }

    }

    public function getId()
    {
        $idArray = array();
        foreach ($this->_id as $id) {
            $idArray[] = substr($id, strrpos($id, '/')+1);
        }
        
        return $idArray;
    }



}
