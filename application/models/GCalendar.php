<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../library/Google/GCalendar.php');

class Model_GCalendar extends Google_GCalendar
{
    protected $_gcal;

    protected $user = 'dykklubben.delfinen@gmail.com';
    protected $holidaysURI = 'http://www.google.com/calendar/feeds/sv.swedish%23holiday@group.v.calendar.google.com/public/basic';
    protected $calendarURI = 'http://www.google.com/calendar/feeds/dykklubben.delfinen@gmail.com/public/basic';
    

    public function __construct($public = true) {

        if ($public == true)
            $this->_gcal = new Google_GCalendar($this->user);
        else
            $this->_gcal = new Google_GCalendar();
    }

    
    public function getNextEvents()
    {
        

        $query = $this->_gcal->getService()->newEventQuery();
        $query->setVisibility('public');
        $query->setUser($this->user);
        $query->setProjection('full');

        #$query->setQuery('Recurrent');
        $query->setOrderby('starttime');
        $query->setFutureevents(true);
        $query->setSortOrder('ascending');
        $query->setSingleEvents(true);
        $query->setMaxResults(10);
        // Retrieve the event list from the calendar server


        $eventFeed = $this->_gcal->getEvents($query);
        
        foreach($eventFeed as $event) {
            
            foreach($event->when as $when) {
                #$id = substr($event->getLink('edit')->href, strrpos($event->getLink('edit')->href, '/')+1);
                $id = substr($event->id->text, strrpos($event->id->text, '/')+1);
                $events[] = array("id" => $id, "title" =>  $event->title->text, "startTime" => $when->startTime, "endTime" => $when->endTime);
            }
            
        }
        
        return $events;
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
