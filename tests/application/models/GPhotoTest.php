<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../application/models/GPhoto.php');
/**
 * Description of GPhotoTest
 *
 * @author Anders
 */
class Model_GPhotoTest extends ControllerTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function testGetRandomPhotos()
    {
        return;
        $gphotos = new Model_GPhoto(true);

        

        $randomPhotos = $gphotos->getRandomPhotos(4);
        var_dump($randomPhotos);
    }
    public function testGetRandomPhotosCached()
    {
        $gphotos = new Model_GPhoto(true);


        $randomPhotos = $gphotos->getRandomPhotosCached(4);
        var_dump($randomPhotos);
        
    }
}
