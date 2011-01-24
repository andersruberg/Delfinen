<?php

require_once('../library/Google/GPhoto.php');

/**
 * Description of GCalendarTest
 *
 * @author Anders
 */
class GPhotoTest extends ControllerTestCase {

    protected $gphoto;
    protected $user = 'dykklubben.delfinen@gmail.com';

    public function setUp() {
        parent::setUp();
    }

    public function testGetPrivateAlbumList() {
        return;
        $this->gphoto = new Google_GPhoto();

        $albumFeed = $this->gphoto->getAlbums();
        echo "\n\nPrivate albums:\n";
        foreach ($albumFeed as $album) {
            echo $album->title->text . "\n";
        }
    }

    public function testChangeAlbumAccess() {
        return;
        $this->gphoto = new Google_GPhoto();

        $albumFeed = $this->gphoto->getAlbums();
        echo "\n\nChangning access rights:\n";
        foreach ($albumFeed as $album) {
            $rights = $album->rights->text;
            $album->rights->text = 'public';
            $updatedAlbum = $album->save();
            echo $album->title->text . " changed from " . $rights ." to " . $updatedAlbum->rights->text . "\n" ;
        }
    }

    public function testGetPublicAlbumList() {
        return;
        $this->gphoto = new Google_GPhoto($this->user);

        $albumFeed = $this->gphoto->getAlbums();
        echo "\n\nPublic albums:\n";
        foreach ($albumFeed as $album) {
            echo $album->title->text . " : " . Google_GPhoto::getId($album->id->text) .  "\n";
        }
    }

    public function testGetLatestPublicPhotos() {
        return;
        $this->gphoto = new Google_GPhoto($this->user);

        $photos = $this->gphoto->getLatestPhotos();
        echo "\n\nLatest public photos:\n";
        foreach ($photos as $photo) {
            echo $photo->title->text . "\n";
        }
    }



    public function testGetLatestPrivatePhotos() {
        return;
        $this->gphoto = new Google_GPhoto();

        $photos = $this->gphoto->getLatestPhotos();
        echo "\n\nLatest private photos:\n";
        foreach ($photos as $photo) {
            echo $photo->title->text . "\n";
        }
    }

    public function testGetPhotosFromPublicAlbumById() {
        return;
        $this->gphoto = new Google_GPhoto($this->user);
        $id = '5448422549360317329';
        $photos = $this->gphoto->getPhotosByAlbumId($id);
        echo "\n\nPhotos from public album with id: " . $id . "\n";
        foreach ($photos as $photo) {
            echo $photo->title->text . " : " . $photo->getMediaGroup()->description->text . "\n";
        }
    }

    public function testGetPhotosFromAlbumById() {
        return;
        $this->gphoto = new Google_GPhoto();

        $id = '5448422549360317329';
        $photos = $this->gphoto->getPhotosByAlbumId($id);
        echo "\n\nPhotos from public album with id: " . $id . "\n";
        foreach ($photos as $photo) {
            echo $photo->title->text . " : " . $photo->getMediaGroup()->description->text . "\n";
        }
    }

    public function testGetAlbumsByRegex() {
        return;
        $this->gphoto = new Google_GPhoto($this->user);
        $albumTitle = '/H/';
        $albums = $this->gphoto->getAlbumsByRegex($albumTitle);
        echo "\n\nPhotos from public album with title: " . $albumTitle . "\n";
        foreach ($albums as $album) {
            echo $album->title->text;
            //. " : " . $photo->getMediaGroup()->description->text . "\n";
            //foreach($album as $)
        }
    }

    public function testGetPhotosFromAlbumByName() {
        return;
        $this->gphoto = new Google_GPhoto($this->user);
        $albumName = "Hägghults stenbrott";
        $photos = $this->gphoto->getPhotosByAlbumTitle($albumName);
        echo "\n\nPhotos from public album with name: " . $albumName . "\n";
        foreach ($photos as $photo) {
            echo $photo->title->text . " : " . $photo->getMediaGroup()->description->text . "\n";
        }
    }
}
