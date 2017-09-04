<?php

	@session_start();

	include("connection.php");
	include("functions.php");

	if(isset($_POST['okLogin'])){

		#Tjek begge felter har en værdi
		if(empty($_POST['mail']) || empty($_POST['upass'])){
			$_SESSION['logFailed'] = true;
			header("location:../preGodmode.php");
		};

		#Klargør lidt inputs
		$uname = mysqli_real_escape_string($conn,$_POST['mail']);
		$upass = mysqli_real_escape_string($conn,md5(sha1($_POST['upass'])));
		$ip = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());

		#Find profilen
		$sql = "SELECT * FROM users WHERE mail='".$uname."' AND upass='".$upass."'";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$num = mysqli_num_rows($query);

		#Hvis data passer overens
		if($num == true){
			$result = mysqli_fetch_array($query);
			$_SESSION['sloaLogged'] = $result['id'];
			$sql = "INSERT INTO events (ip,time,uid,event,rel) VALUES ('".$ip."','".$time."',".$result['id'].",'Succesfuldt login','login')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
			header("location:../godmode.php");

		#Hvis oplysningerne ikke passer overens
		}else{
			$sql = "INSERT INTO events (ip,time,event,danger,rel) VALUES ('".$ip."','".$time."','Mislykket login',1,'login')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$_SESSION['logFailed'] = true;
			header("location:../preGodmode.php");
		};
	}else{
		header("location:../");
	};

?>