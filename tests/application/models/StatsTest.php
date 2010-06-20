<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

require_once('../application/models/GPhoto.php');

/**
 * Description of StatsTests
 *
 * @author Anders
 */
class Model_StatsTest extends ControllerTestCase {

    public function setUp() {
        parent::setUp();


    }

    public function testCanDoUnitTest() {

        return;
        $gPhoto = new Model_GPhoto();
       
        
        $baseLocation = "c:\\delfinen\\";

        $this->SQLConnect();

        $query = "SELECT * from gallery ORDER BY indexid desc";
        $albumResult = mysql_query($query) or die ("Query failed");
        $num_albums = mysql_num_rows($albumResult);

        echo "Number of albums: $num_albums \n";

        while ($album = mysql_fetch_array($albumResult, MYSQL_ASSOC)) {

            echo $album['indexid'] . " " . $album['description'] . "\n";



            $query = "SELECT * FROM file WHERE galleryid=" . $album['indexid'] . " ORDER BY id ASC";
            $result = mysql_query($query) or die ("Query failed");
            $num_rows = mysql_num_rows($result);

            echo ": " . $num_rows . " pictures. \n";

            $albumName = $album['description'];
            $gPhotoAlbumId = $gPhoto->createAlbum(utf8_encode($albumName));

            if ($gPhotoAlbumId == null)
                die("Didn't get album id from GPhoto");
            
            $location = $baseLocation . $albumName;
            
            mkdir($location) or die("Could not create directory " . $location);

            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                if ($row['datatype']=='video/mpeg' or $row['datatype']=='application/octet-stream' or $row['datatype']=='video/avi') {

                } else {

                    $filename= $location . "\\" . $row['name'];
                    $file=fopen($filename,'w');

                    $data = $this->download($row['id']);
                    if (fwrite($file,$data)) {
                       
            
                        $gPhoto->uploadPhoto($gPhotoAlbumId, "default", $filename, utf8_encode($row['name']), utf8_encode($row['description']));
                        echo "      " . $filename . " created sucessfully \n";
                    }
                    else {
                        die("Failed to create file: " . $row["name"]);
                    }


                }

            }
            
        }

        $this->assertTrue(true);

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
    
    public function download($id) {
        
        
        #include 'commoninclude.php';
        
        $nodelist = array();
        
// Pull file meta-data
        $this->SQLConnect();
        $query = "SELECT * FROM file WHERE id = " . $id;
        if (!$RES = mysql_query($query)) {
            die("Failure to retrive file metadata");
        }
        
        if (mysql_num_rows($RES) != 1) {
            die("Not a valid file id!");
        }
        
        $FileObj = mysql_fetch_object($RES);
        
// Pull the list of file inodes
        $query = "SELECT id FROM filedata WHERE masterid = " . $id . " order by id";
        
        if (!$RES = mysql_query($query)) {
            die("Failure to retrive list of file inodes");
        }
        
        while ($CUR = mysql_fetch_object($RES)) {
            $nodelist[] = $CUR->id;
        }
        
// Loop thru and stream the nodes 1 by 1
        
        $data = array();
        for ($Z = 0 ;$Z < count($nodelist) ;$Z++) {
            $query = "SELECT filedata FROM filedata WHERE id = " . $nodelist[$Z];
            
            if (!$RESX = mysql_query($query)) {
                die("Failure to retrive file node data");
            }
            
            $DataObj = mysql_fetch_object($RESX);
            $data[] = $DataObj->filedata;
        }
        
        return implode('',$data);
    }

}

