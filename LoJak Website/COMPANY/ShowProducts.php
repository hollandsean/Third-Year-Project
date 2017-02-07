<html>
   <head>
      <title>The jQuery Example</title>
      <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <script type="text/javascript" language="javascript">
         function addToCart(id,fieldname){
            var num = document.getElementById(fieldname).value;
            $.post( 
                  "updatecart.php",
                  { prodid: id,
                    qty: num },
                  function(data) {
                    // uncomment this line to see php response 
 //$('#stage').html(data);
                  }
               );
         }
   </script>
         <script type="text/javascript" language="javascript">
         function addToCart(id,fieldname){
            var num = document.getElementById(fieldname).value;
            $.post( 
                  "updatecart.php",
                  { prodid: id,
                    qty: num },
                  function(data) {
                    // uncomment this line to see php response 
 //$('#stage').html(data);
                  }
               );
         }
	
         
   </script>
   </head>
<body>	

<p>
				<a href="Index.html" class="header">Home Page</a>
			</p>
<div id="stage" style="background-color:cc0;">
         
      </div>

<form action="ShowProducts.php" method="post">
<div id="userdetails">
Username:<input type="text" name="username" id="username" value="" />
 <input type="submit" value="Login">
</form>		       
</div>

<?php
// Start the session
session_start();
if (isset($_POST["username"]))
{
$_SESSION["username"] = $_POST["username"];

}
if (isset($_SESSION["username"])){
    $username = $_SESSION["username"]; 
    echo "<h1> You are logged in as $username </h1>";
}

require_once 'db_login.php';
$db_server = mysql_connect($db_hostname, $db_username, $db_password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());

mysql_select_db($db_database)
or die("Unable to select database: " . mysql_error());

$query = "SELECT * FROM products";
$result = mysql_query($query);

if (!$result) die ("Database access failed: " . mysql_error());

$rows = mysql_num_rows($result);
echo "<table border=1><tr> <th>Description</th> <th>Price</th><th>Image</th><th>Quantity</th><th>Purchase</th></tr>";
$count=0;

	for ($i = 0; $i < 6; $i++){
	//while ($row = mysql_fetch_assoc($result) && $count < 6) {
		$row = mysql_fetch_assoc($result);
		echo "<tr>";
			echo "<td>".$row['name']."</td>";
			echo "<td>".$row["price"]."</td>";
			echo '<td><img src="images/'.$row["imagefilename"].'" alt="Smiley face" height="100" width="100">' ."</td>";
			echo '<td>';
			echo  '<input type="text" size="2" maxlength="2" id="qty'.$count.'" value="1" /></td>';
			
			echo '<input type="hidden" name="id" value="'.$row['productID'].'"/>';
			echo '<td><input type="button" id="driver" value="Buy" onclick="addToCart(';
			echo ''.$row['productID'].','."'qty".$count."')".'"/></td>'; 
		echo "</tr>";
		$count++;
	}

echo "</table>";
echo '<form method="post" action="displaycart.php">';
echo '<button type="submit">Display Cart</button></form>'; 
?>

</body>
	
</html>
