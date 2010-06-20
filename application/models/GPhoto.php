<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

require_once('../library/Google/GPhoto.php');

class Model_GPhoto {

    protected $user = 'dykklubben.delfinen@gmail.com';
    protected $_gphoto;

    public function __construct($public = true) {

        if ($public == true)
            $this->_gphoto = new Google_GPhoto($this->user);
        else
            $this->_gphoto = new Google_GPhoto();
    }

    public function getRandomPhotos($nbrOfPhotos = 10) {
        //Get a list of albums
        $albums = $this->_gphoto->getAlbums();
        $albumIds = array();
        foreach ($albums as $album) {
            $id = Google_GPhoto::getId($album->id->text);
            
            $albumIds[] = $id;
        }
        //Pick a random album
        $randomAlbumIds = array_rand($albumIds, $nbrOfPhotos);
        

        if (!is_array($randomAlbumIds))
            $randomAlbumIds = array($randomAlbumIds);
        
        $result = array();

        //Get the photos in the random album
        foreach ($randomAlbumIds as $entry) {
            $photos = $this->_gphoto->getPhotosByAlbumId($albumIds[$entry]);
            $photoIds = array();
            foreach ($photos as $photo) {
                $id = Google_GPhoto::getId($photo->id->text);
                $photoIds[] = $id;
            }

            //Get a random photo
            $randomPos = array_rand($photoIds, 1);
            $photo = $photos[$randomPos];

            //$photo = $this->_gphoto->getPhotoById($photoId, albumIds[$entry]);
            $photoId = $photoIds[$randomPos];
            $photoTitle = $photo->title->text;
            $photoDescription = $photo->getMediaGroup()->description->text;
            $photoThumbnails = $photo->getMediaGroup()->getThumbnail();
            $photoThumbnailUrl = $photoThumbnails[1]->getUrl();
            $result[] = array("photoId" => $photoId, "albumId" => $albumIds[$entry], "title" => $photoTitle, "description" => $photoDescription, "thumbnailUrl" => $photoThumbnailUrl);
        }
        return $result;
    }

    /**
     * Return all photos in the album as an array with
     * title, thumbnailUrl etc.
     *
     * @param <type> $albumTitle
     * @return <type>
     */
    /*public function getPhotosByAlbumTitle($albumTitle) {

        $query = new Zend_Gdata_Photos_AlbumQuery();
        $query->setType("entry");
        $query->setAlbumName($albu
     * mTitle);
        try {
            $albumFeed = $this->_service->getAlbumFeed($query);

            $albumEntries = array();
            
            foreach ($albumFeed as $albumEntry) {

                $title = $albumEntry->title->text;
                var_dump($title);
                $thumbnailArray = $albumEntry->getMediaGroup()->getThumbnail();
                $thumbnailUrl = $thumbnailArray[1]->getUrl();
                $contentArray = $albumEntry->getMediaGroup()->getContent();
                $contentUrl = $contentArray[0]->getUrl();
                
                $albumEntries[] = array('title' => $title, 'thumbnailUrl' => $thumbnailUrl, 'contentUrl' => $contentUrl);
                var_dump($albumEntries);
            }

            return $albumEntries;
        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getPhotosByAlbumId($id) {

        $query = new Zend_Gdata_Photos_AlbumQuery();
        $query->setType("entry");
        $query->setAlbumId($id);
        try {
            $albumFeed = $this->_service->getAlbumFeed($query);

            $albumEntries = array();

            foreach ($albumFeed as $albumEntry) {

                $title = $albumEntry->title->text;

                $thumbnailArray = $albumEntry->getMediaGroup()->getThumbnail();
                $thumbnailUrl = $thumbnailArray[1]->getUrl();
                $contentArray = $albumEntry->getMediaGroup()->getContent();
                $contentUrl = $contentArray[0]->getUrl();

                $albumEntries[] = array('title' => $title, 'thumbnailUrl' => $thumbnailUrl, 'contentUrl' => $contentUrl);

            }

            return $albumEntries;
        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getLatestPhotos() {
        $query = $this->_service->newUserQuery();
        $query->setUser("default");
        $query->setKind("photo");
        $query->setMaxResults("4");

        try {
            // because we specified 'photo' for the kind, only PhotoEntry objects
            // will be contained in the UserFeed
            $userFeed = $this->_service->getUserFeed(null, $query);



            foreach($userFeed as $entry) {
                $thumbnailArray = $entry->getMediaGroup()->getThumbnail();
                $this->_thumbnailUrl[] = $thumbnailArray[1]->getUrl();
                $this->_albumId[] = $entry->getGphotoAlbumId();

            }

            return $userFeed;
        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getThumbnailUrl() {
        return $this->_thumbnailUrl;
    }

    public function getAlbumThumbnail($albumName) {
        $this->getPhotosByAlbumTitle($albumName);
    }

    public function getAlbums() {

        try {
            $userFeed = $this->_service->getUserFeed("default");
            foreach ($userFeed as $userEntry) {
                $title = $userEntry->title->text;
                $id = $userEntry->id;

            }
            return $userFeed;
        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getAll() {

        try {
            $userFeed = $this->_service->getUserFeed("default");

            $albums = array();

            foreach ($userFeed as $userEntry) {
                $title = $userEntry->title->text;
                $id = $this->getId($userEntry->id);
                $albumEntries = $this->getPhotosByAlbumId($id);
                $albums[] = array('title' => $title, 'id' => $id, 'entries' => $albumEntries);
            }
            return $albums;
        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteAll() {

        try {
            $userFeed = $this->_service->getUserFeed("default");

            foreach ($userFeed as $userEntry) {
                $userEntry->delete();
            }

        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getId($ID)
    {
        return substr($ID, strrpos($ID, '/')+1);
    }


    public function createAlbum($name) {

        $entry = new Zend_Gdata_Photos_AlbumEntry();
        $entry->setTitle($this->_service->newTitle($name));
        $entry->setSummary($this->_service->newSummary(""));

        $createdEntry = $this->_service->insertAlbumEntry($entry);

        return $this->getId($createdEntry->id->text);
    }

    public function uploadPhoto($albumId = "default", $username = "default", $filename = null, $photoName = null, $photoCaption = null, $photoTags = null) {


// Set albumId to 'default' to indicate that we'd like to upload
// this photo into the 'drop box'.  This drop box album is automatically
// created if it does not already exist.

        $fd = $this->_service->newMediaFileSource($filename);
        $fd->setContentType("image/jpeg");

// Create a PhotoEntry
        $photoEntry = $this->_service->newPhotoEntry();

        $photoEntry->setMediaSource($fd);
        $photoEntry->setTitle($this->_service->newTitle($photoName));
        $photoEntry->setSummary($this->_service->newSummary($photoCaption));

// add some tags
        $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
        $keywords->setText($photoTags);
        $photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
        $photoEntry->mediaGroup->keywords = $keywords;

// We use the AlbumQuery class to generate the URL for the album
        $albumQuery = $this->_service->newAlbumQuery();

        $albumQuery->setUser($username);
        $albumQuery->setAlbumId($albumId);

// We insert the photo, and the server returns the entry representing
// that photo after it is uploaded
        $insertedEntry = $this->_service->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl());
    }
    */
}
