<?php

require_once('../library/Google/GBlogger.php');

/**
 * Description of GBloggerTest
 *
 * @author Anders
 */
class GBloggerTest extends ControllerTestCase {

    protected $gblogger;
    protected $user = '03015977435116413044';

    public function setUp() {
        parent::setUp();
    }

    public function testGetBlogId() {
        return;
        $this->gblogger = new Google_GBlogger($this->user);
        $id = $this->gblogger->getBlogID();
        echo "\n\nBlog id: " . $id . "\n";
    }


    public function testGetBlogPosts() {
        return;
        $this->gblogger = new Google_GBlogger($this->user);
        $blogPosts = $this->gblogger->getAllBlogPosts();

        echo "\n\nGetting all blog posts\n";
        foreach ($blogPosts->entries as $blogPost) {
            echo $blogPost->title->text . "\n";
        }
    }

    public function testGetPrivateBlogPosts() {
        return;
        $this->gblogger = new Google_GBlogger('default', 'blogger');
        $blogPosts = $this->gblogger->getAllBlogPosts();

        echo "\n\nGetting all blog posts\n";
        foreach ($blogPosts->entries as $blogPost) {
            echo $blogPost->title->text . "\n";
        }
    }

    public function testDeleteAllBlogPosts() {
        return;
        $this->gblogger = new Google_GBlogger('default', 'blogger');
        $blogPosts = $this->gblogger->getAllBlogPosts();

        echo "\n\nDeleting all blog posts\n";
        foreach ($blogPosts->entries as $blogPost) {
            $id = Google_GBlogger::getID($blogPost->id->text);
            $this->gblogger->deletePost($id);
            echo $blogPost->title->text . " has been deleted\n";
        }
    }

    public function testCreatePublishedBlogPost() {
        return;
        $this->gblogger = new Google_GBlogger('default', 'blogger');
        $newBlogPost = $this->gblogger->createPublishedPost('Testar api', 'Detta är bara ett test');
        echo "Id of new blog post is: " . $newBlogPost . "\n";

    }
    
    public function testMigrateBlogPosts() {
        
        $this->SQLConnect();
        $this->gblogger = new Google_GBlogger('default', 'blogger');
        

        $query = "SELECT * from d_news";
        $result = mysql_query($query) or die ("Query failed");
        $num_posts = mysql_num_rows($result);

        echo "\n\nNumber of blog posts to migrate: $num_posts \n";
        
        while ($post = mysql_fetch_array($result, MYSQL_ASSOC)) {

            $id = $post['indexid'];
            $title = utf8_encode($post['newstitle']);
            $content = utf8_encode($post['newstext']);
            $date = $post['datum'];
            $time = $post['tid'];
            $dateTime = new Zend_Date($date . " " . $time);
            $newBlogPost = $this->gblogger->createPublishedPost($title, $content, $dateTime);
            echo "Id of new blog post is: " . $newBlogPost . "\n";
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