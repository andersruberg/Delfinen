<?php

$sqlserver = 'localhost';
$sqlport = '';
$sqluser = 'root';
$sqlpassword = '';
$sqldatabase = 'delfinen';

function SQLConnect()
{
global $sqlserver, $sqlport, $sqluser, $sqlpassword, $sqldatabase;

$link = mysql_connect("$sqlserver", "$sqluser", "$sqlpassword")
        or die("Could not connect to SQL server");
mysql_select_db("$sqldatabase") or die("Database unreachable");
mysql_set_charset('latin1',$link);
return $link;
}


?>