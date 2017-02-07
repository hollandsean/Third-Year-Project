<?php
/*
Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password)
*/
require_once 'db_login.php';
$con = mysql_connect($db_hostname, $db_username, $db_password);
if (!$con) die("Unable to connect to MySQL: " . mysql_error());

 
$db_select=mysql_select_db("company", $con);

 if (!$db_select){
    echo("Could not select the database: <br />". mysql_error());
}
$name = $_POST['name'];
$address = $_POST['address'];
$age = $_POST['age'];

 
// attempt insert query execution
$sql="INSERT INTO customer (name, age, address)
 VALUES ('$name','$age','$address')";
echo $sql;
// Insert one record
if (mysql_query($sql, $con)) {
    echo "Added record to products\n <br>";
} else {
    echo 'Error adding a record: ' . mysql_error() . "\n";
}
 
// close connection
mysql_close($con);
?>

<p>
	<a href="Index.html" class="header">Home Page</a>
</p>