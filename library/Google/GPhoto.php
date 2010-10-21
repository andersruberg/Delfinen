<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

require_once('../library/Google/GData.php');

class Google_GPhoto extends Google_GData {

    protected $_user;

    public function __construct($user = 'default') {

        if ($user == 'default') //Authenticated session
            parent::__construct(Zend_Gdata_Photos::AUTH_SERVICE_NAME);

        $this->_user = $user;
        Zend_Loader::loadClass('Zend_Gdata_Photos');
        $this->_service = new Zend_Gdata_Photos($this->_client);

        
    }

    /**
     * Return all photos in the album as an array with
     * title, thumbnailUrl etc.
     *
     * @param <type> $albumTitle
     * @return <type>
     */
    public function getAlbumsByRegex($albumTitle) {

        /*$query = new Zend_Gdata_Photos_AlbumQuery();
        $query->setType("entry");
        $query->setUser($this->_user);
        $query->setAlbumName($albumTitle);
        echo $query->getQueryUrl();*/
        try {
            $albumFeed = $this->_service->getUserFeed($this->_user);
            foreach ($albumFeed as $album) {
                $title = $album->title->text;
                if (preg_match($albumTitle, $title))
                        $albums[] = $album;
            }
            return $albums;

        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * TODO: Replace å,ä,ö and spaces in URI
     * @param <type> $albumTitle
     * @return <type> 
     */
    public function getPhotosByAlbumTitle($albumTitle) {

        $query = new Zend_Gdata_Photos_AlbumQuery();
        $query->setType("entry");
        $query->setUser($this->_user);
        $albumTitle = preg_replace("[^A-Za-z0-9]", "", $albumTitle);
        $query->setAlbumName($albumTitle);
        echo $query->getQueryUrl();
        try {
            return $this->_service->getUserFeed($this->_user, $query);
            

        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    public function getPhotosByAlbumId($id) {

        $query = new Zend_Gdata_Photos_AlbumQuery();
        $query->setType("entry");
        $query->setUser($this->_user);
        $query->setAlbumId($id);
        
        try {
            return $this->_service->getAlbumFeed($query);


        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getPhotoById($photoId, $albumId) {

        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setType("entry");
        $query->setUser($this->_user);
        $query->setPhotoId($photoId);
        $query->setAlbumId($albumId);
     

        try {
            return $this->_service->getPhotoEntry($query);
            
            

        }
        catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getLatestPhotos($query = null) {
    if (!isset ($query)) {
        $query = $this->_service->newUserQuery();
        $query->setMaxResults("4");
        
    }

    $query->setUser($this->_user);
    $query->setKind("photo");


    try {
        // because we specified 'photo' for the kind, only PhotoEntry objects
        // will be contained in the UserFeed
        return $this->_service->getUserFeed($this->_user, $query);

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
            return $this->_service->getUserFeed($this->_user);
        
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

    public static function getId($ID)
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

}
