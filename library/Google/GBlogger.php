<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
require_once('../library/Google/GData.php');
/**
 * Description of GBlogger
 *
 * @author Anders
 */
class Google_GBlogger extends Google_GData {

    protected $_blogID;
    protected $_blogUri;
    protected $_user;


    public function __construct($user = 'default', $authServiceName = null) {

        parent::__construct($authServiceName);
        
        $this->_service = new Zend_Gdata($this->_client);

        $this->_user = $user;
        $this->_blogID = $this->getBlogID();
        $this->_blogUri = 'http://www.blogger.com/feeds/'. $this->_blogID . '/posts/default';
    }

    public function getBlogID($index = 0) {
        $query = new Zend_Gdata_Query('http://www.blogger.com/feeds/' . $this->_user . '/blogs');
        $feed = $this->_service->getFeed($query);

        return $this->getID($feed->entries[$index]->id->text);

    }

    public function getAllBlogPosts() {
        $query = new Zend_Gdata_Query($this->_blogUri);
        return $this->_service->getFeed($query);
        
    }

    public function getLatestBlogPosts($labels = null, $maxResults = null) {
       
        $query = new Zend_Gdata_Query($this->_blogUri);
        if (isset ($maxResults))
            $query->setParam('max-results', $maxResults);
        if (isset ($labels))
            $query->setParam('category', $labels);
        
        return $this->_service->getFeed($query);

    }

    public static function getID($text)
    {
        $idText = explode('-', $text);
        return $idText[2];
    }

    public function createPublishedPost($title = null, $content = null, $dateTime = null) {
        
        $entry = $this->_service->newEntry();
        $entry->title = $this->_service->newTitle($title);
        if (isset($dateTime)) {
            $isoDateTime = $dateTime->get('c');
            $publishedDatetime = new Zend_Gdata_App_Extension_Published($isoDateTime);
            $entry->setPublished($publishedDatetime);
        }
        $entry->content = $this->_service->newContent($content);
        $entry->content->setType('text');

        $createdPost = $this->_service->insertEntry($entry, $this->_blogUri);
        $idText = explode('-', $createdPost->id->text);
        $newPostID = $idText[2];

        return $newPostID;
    }

    public function getPostComments($postID)
    {
        $query = new Zend_Gdata_Query($this->_blogUri . '/' . $postID . '/comments/default');
        return $this->_service->getFeed($query);

        
    }

    public function createPostComment($postID, $content)
    {
        $uri = $this->_blogUri . '/' . $postID . '/comments/default';
        $newComment = $this->_service->newEntry();
        $newComment->content = $gdClient->newContent($commentText);
        $newComment->content->setType('text');
        $createdComment = $this->_service->insertEntry($newComment, $uri);

        $editLink = explode('/', $createdComment->getEditLink()->href);
        $newCommentID = $editLink[8];

        return $newCommentID;

    }

    public function deletePost($postID)
    {
        $uri = $this->_blogUri . "/" . $postID;
        $this->_service->delete($uri);
    }

    public function deleteComment($postID, $commentID)
    {
        $uri = $this->_blogUri . '/' . $postID . '/comments/default' . $commentID;
        $this->_service->delete($uri);
    }




}

