<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../application/models/GBlogger.php');
/**
 * Description of GBloggerTest
 *
 * @author Anders
 */
class Model_GBloggerTest extends ControllerTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testGetAllBlogPosts()
    {
        $gblogger = new Model_GBlogger();
        $blogPosts = $gblogger->getAllBlogPosts();
        foreach ($blogPosts as $blogPost) {
            echo $blogPost['title'] . "\n";
        }
    }
}
