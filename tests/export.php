<?php
include_once 'commoninclude.php';
include_once 'download.php';
include_once '/../application/models/GData.php';
include_once '/../application/models/GPhoto.php';

$baseLocation = "c:/delfinen/";
$photos = new Model_GPhoto();

die();
SQLConnect();

$query = "SELECT * from gallery ORDER BY indexid desc";
$albumResult = mysql_query($query) or die ("Query failed");
$num_albums = mysql_num_rows($albumResult);

echo "Number of albums: $num_albums";

while ($album = mysql_fetch_array($albumResult, MYSQL_ASSOC)) {

   echo $album['indexid'];

   echo " ";
    echo $album['description'];
    #echo mb_convert_encoding($album['description'], "pass", "auto");
   

    $query = "SELECT * FROM file WHERE galleryid=" . $album['indexid'] . " ORDER BY id ASC";
    $result = mysql_query($query) or die ("Query failed");
    $num_rows = mysql_num_rows($result);

    echo ": " . $num_rows . " pictures.";
    echo "\n";

    
    $albumName = $album['description'];
    $location = $baseLocation . $albumName;
    
    mkdir($location) or die("Could not create directory " . $album['description']);

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        if ($row[datatype]=='video/mpeg' or $row[datatype]=='application/octet-stream' or $row[datatype]=='video/avi') {

        } else {

            $filename= $location . "/" . $row['name'];
            $file=fopen($filename,'w');
            
            $data = download($row['id']);
            if (fwrite($file,$data)) {
                echo "      " . $filename . " created sucessfully \n";

            }
            else {
                die("Failed to create file: " . $row["name"]);
            }
            
        }

    }
    die();

}
?>
