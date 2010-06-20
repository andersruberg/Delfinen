<?php

require_once('../library/Google/GCalendar.php');

/**
 * Description of GCalendarTest
 *
 * @author Anders
 */
class GCalendarTest extends ControllerTestCase {

    protected $gcal;

    public function setUp() {
        parent::setUp();
    }

    public function testGetCalendars() {
        return;
        $this->gcal = new Google_GCalendar(Zend_Gdata_Calendar::AUTH_SERVICE_NAME);

        $calendars = $this->gcal->getCalendars();

        $expectedTitles = array('dykklubben.delfinen@gmail.com', 'Svenska helgdagar', 'Veckonummer');
        $receivedTitles = array();
        foreach ($calendars as $calendar) {
            $receivedTitles[] = $calendar->title->text;
        }
        $this->assertEquals($receivedTitles, $expectedTitles);
    }

    public function testCannotGetCalendars() {
        return;
        $this->gcal = new Google_GCalendar();

        try {
            $calendars = $this->gcal->getCalendars();
            $this->assertTrue(false);
        }
        catch (Zend_Gdata_App_HttpException $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetPublicEvents() {
        return;
        $this->gcal = new Google_GCalendar();

        $events = $this->gcal->getPublicEvents('dykklubben.delfinen@gmail.com');

        foreach ($events as $event) {
            echo $event->title->text . "\n";
        }

        echo "\n\n";


    }

    public function testGetHolidayEvents() {
        return;
        $this->gcal = new Google_GCalendar();

        $events = $this->gcal->getPublicEvents('sv.swedish%23holiday@group.v.calendar.google.com');

        foreach ($events as $event) {
            foreach ($event->when as $when) {
                $tmpTime = new Zend_Date($when->startTime);
                $startTime = $tmpTime->get('Y-M-d : H:m');
                $tmpTime = new Zend_Date($when->endTime);
                $endTime = $tmpTime->get('Y-M-d : H:m');
                echo $event->title->text . " : " . $startTime . " - " . $endTime . "\n";
            }
        }

        echo "\n\n";
    }

    public function testGetWeeknumberEvents() {
        return;
        $this->gcal = new Google_GCalendar();

        $events = $this->gcal->getPublicEvents('e_2_sv%23weeknum@group.v.calendar.google.com');

        foreach ($events as $event) {
            foreach ($event->when as $when) {
                $tmpTime = new Zend_Date($when->startTime);
                $startTime = $tmpTime->get('Y-M-d : H:m');
                $tmpTime = new Zend_Date($when->endTime);
                $endTime = $tmpTime->get('Y-M-d : H:m');
                echo $event->title->text . " : " . $startTime . " - " . $endTime . "\n";
            }
        }

        echo "\n\n";
    }

    public function testRecurrentEvents() {
        return;
        $this->gcal = new Google_GCalendar();

        $query = $this->gcal->getService()->newEventQuery();
        $query->setVisibility('public');
        $query->setUser('dykklubben.delfinen@gmail.com');
        $query->setProjection('full');

        #$query->setQuery('Recurrent');
        $query->setOrderby('starttime');
        $query->setFutureevents(true);
        $query->setSortOrder('ascending');
        $query->setSingleEvents(true);
        $query->setMaxResults(10);
        // Retrieve the event list from the calendar server


        $events = $this->gcal->getEvents($query);

        foreach ($events as $event) {
            echo $event->title->text . "\n";
            foreach($event->when as $when) {
                echo $when->startTime . "\n";
                echo $when->endTime . "\n";
            }
        }

        echo "\n\n";


    }


}

