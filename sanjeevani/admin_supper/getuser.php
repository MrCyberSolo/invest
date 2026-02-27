<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php

$q = intval(substr($_GET['q'],0));

include('includes/connect.php');
//$con = mysqli_connect('localhost','root','','asrgroup');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

//mysqli_select_db($con,"ajax_demo");
$sql="SELECT * FROM user WHERE email = '".$q."'";
$result = mysqli_query($con,$sql);

if(mysqli_num_rows($result)>0) { 
while($row = mysqli_fetch_array($result)) {
  

    echo "<b>Name: " . $row['name'] . " </b>";
    
} } else { echo "Invalid User ID"; }

mysqli_close($con);

?>
</body>
</html>