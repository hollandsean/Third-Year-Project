	<head>
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

<form action="displaycart.php" method="post">
<div id="userdetails">
Username:<input type="text" name="username" id="username" value="" />
 <input type="submit" value="Login">
</form>		       
</div>

<?php
session_start();
if (!isset($_SESSION["cart"]))
   $_SESSION["cart"] = array();

if (isset($_POST["username"]))
{
$_SESSION["username"] = $_POST["username"];

}
if (isset($_SESSION["username"])){
    $username = $_SESSION["username"]; 
    echo "<h1> You are logged in as $username </h1>";
}

if(isset($_POST['delete']))

 {

 array_splice($_SESSION["cart"], $_POST["rownumber"],1);

 

 }

$rows = count($_SESSION["cart"]);
$cnt=0;
$total=0;
echo "<table border=1><tr> <th>description</th> <th>quantity</th><th>Image</th><th>Total Price</th></tr>";
while ($cnt < $rows) {
    $row = $_SESSION["cart"][$cnt];
    echo "<tr>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row["qty"]."</td>";
    echo '<td><img src="images/'.$row["imagefilename"].'" alt="Smiley face" height="100" width="100">' ."</td>";
	$totalPrice = $row['qty'] * $row['price'];
	echo '<td>'.$totalPrice.'</td>';
	$total += $totalPrice;
	
	echo '<td><form action="displaycart.php" method="post">';

    echo '<input type="hidden" name="rownumber" value="'.$cnt.'" />';

    echo '<input type="submit" name="delete" value="Delete" /></form></td>';
    
    echo "</tr>";
    $cnt++;
	
	require_once 'db_login.php';
	$con = mysql_connect($db_hostname, $db_username, $db_password);
	if (!$con) die("Unable to connect to MySQL: " . mysql_error());

	 
	$db_select=mysql_select_db("company", $con);

	 if (!$db_select){
		echo("Could not select the database: <br />". mysql_error());
	}

	//$customer = $_SESSION["username"];

	
	
}
echo "<tr>	<td></td><td></td><td></td><td>".$total."</td>	</tr>";

if(isset($_POST['checkout']))
	{
		/*Put your code in here to create a new row in the orders table. */
		$query = "SELECT * FROM customer WHERE name = '".$username."';";
		$result = mysql_query($query);

		if (!$result) die ("Database access failed: " . mysql_error());
				
			$rows = mysql_num_rows($result);
				while ($row = mysql_fetch_assoc($result)) 
				{
					$username = $row['name'];
				}
				$sql="INSERT INTO orders (name, total)
					VALUES ('".$username."', '".$total."')";
					
				if (mysql_query($sql, $con)) 
				{
					echo "Added record to orders\n <br>";
				} 
				else 
				{
					echo 'Error adding a record: ' . mysql_error() . "\n";
				}
	}

echo "</table>";
?>


<form action="displaycart.php" method="post">

 <input type="submit" name="checkout" value="Checkout" />
</form>


</body>