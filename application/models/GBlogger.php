<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

require_once('../library/Google/GBlogger.php');

/**
 * Description of GBlogger
 *
 * @author Anders
 *
 */
class Model_GBlogger {

    protected $_gblogger;
    protected $_user = '03015977435116413044';

    public function __construct($public = true) {

        if ($public == true)
            $this->_gblogger = new Google_GBlogger($this->_user);
        else
            $this->_gblogger = new Google_GBlogger();
    }

    public function getAllBlogPosts() {

        $blogPosts = $this->_gblogger->getAllBlogPosts();

        $result = array();
        foreach ($blogPosts->entries as $blogPost) {
            $result[] = array("title" => $blogPost->title->text, "content" => $blogPost->content->text);
        }
        
        return $result;
    }

    public function getLatestEntries($labels = null, $maxResults = null) {

        $blogPosts = $this->_gblogger->getLatestBlogPosts($labels, $maxResults);

        $result = array();
        foreach ($blogPosts->entries as $blogPost) {
            $result[] = array("title" => $blogPost->title->text, "content" => $blogPost->content->text, "published"=>date('Y-m-d H:i:s', strtotime($blogPost->published)));
        }

        return $result;
    }

    /*public function convertEntryToArray($entry){
        $result = array();

        $result = array("id" => $entry->id->text,
                        "title" => $entry->title->text,
                        "content" => $entry->);
    }*/

    
}

