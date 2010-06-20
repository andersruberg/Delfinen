<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../application/models/GCalendar.php');
/**
 * Description of GCalendarTest
 *
 * @author Anders
 */
class Model_GCalendarTest extends ControllerTestCase {

    public function setUp() {
        parent::setUp();


    }

    public function testGetNextEvents()
    {
        return;
        $cal = new Model_GCalendar();
        $nextEvents = $cal->getNextEvents();
        var_dump($nextEvents);
    }
}
