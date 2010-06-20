<?php

function download($id) {


    #include 'commoninclude.php';

    $nodelist = array();

// Pull file meta-data
    SQLConnect();
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

    $data;
    for ($Z = 0 ;$Z < count($nodelist) ;$Z++) {
        $query = "SELECT filedata FROM filedata WHERE id = " . $nodelist[$Z];

        if (!$RESX = mysql_query($query)) {
            die("Failure to retrive file node data");
        }

        $DataObj = mysql_fetch_object($RESX);
        $data = $data . $DataObj->filedata;
    }

    return $data;
}
?>