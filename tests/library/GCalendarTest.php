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

    public function testCreateSingleEvent() {

        
        //Get an authenticated session
        $this->gcal = new Google_GCalendar();

        $newEventId = $this->gcal->createEvent('Test', 'Det här är ett test', 'Ingenstans', '2010-11-27', '11:00', '2010-11-27', '12:00');

        echo "\nA new calender event was created with id: " . $newEventId;


    }

    public function testMigrateCalendarEvents() {
        return;
        $this->SQLConnect();
        $this->gcal = new Google_GCalendar();


        $query = "SELECT * from d_6";
        $result = mysql_query($query) or die ("Query failed");
        $num_posts = mysql_num_rows($result);

        echo "\n\nNumber of calendar events to migrate: $num_posts \n";

        while ($event = mysql_fetch_array($result, MYSQL_ASSOC)) {

            $id = $event['indexid'];
            $title = utf8_encode($event['newstitle']);
            $content = utf8_encode($event['newstext']);
            $date = $event['datum'];
            $time = $event['tid'];
            $dateTime = new Zend_Date($date . " " . $time);
            $newEventId = $newEventId = $this->gcal->createEvent('Test', 'Det här är ett test', 'Ingenstans', '2010-07-27', '10:00', '2010-07-27', '11:00', '+02');
            echo "Id of new blog post is: " . $newEventId . "\n";
            $deleteQuery = "DELETE from d_news WHERE indexid =" . $id;
            mysql_query($deleteQuery) or die ("Failed to delete indexid " . $id);
        }

    }

       public function SQLConnect() {

        $sqlserver = 'localhost';
        $sqlport = '';
        $sqluser = 'root';
        $sqlpassword = '';
        $sqldatabase = 'delfinen';


        $link = mysql_connect("$sqlserver", "$sqluser", "$sqlpassword")
                or die("Could not connect to SQL server");
        mysql_select_db("$sqldatabase") or die("Database unreachable");
        var_dump(mysql_client_encoding($link));

        #mysql_set_charset('utf8',$link);
        return $link;
    }


}

