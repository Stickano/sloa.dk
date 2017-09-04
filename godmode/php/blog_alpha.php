<?php

include("../../php/connection.php");

echo'<div style="position:absolute; width:2px; min-height:100%; background-color:black; left:50%;">&nbsp;</div>';


$sql = "SELECT * FROM blog ORDER BY id DESC";
$query = mysqli_query($conn,$sql) or die(mysqli_error($conn));
$br = 1;
while($result = mysqli_fetch_array($query)){

	
	// Left
	echo'<div style="float:left; width:30%; min-width:250px;">';
		
	echo'</div>';


	// Right
	echo'<div style="float:left; width:30%; min-width:250px;">';
		
	echo'</div>';
	$br++;
};