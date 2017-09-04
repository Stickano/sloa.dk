<?php

	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/updateMeta.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../../");
		exit;
	};


	#Ikke foretag noget hvis det er gæsteprofilen
	if($_SESSION['sloaLogged'] == 2){
		$_SESSION['guestWarning'] = true;
		header("location:".$_SESSION['last']);
		exit;
	};


	#Det her er lavet på en lidt sjov måde - for at få lidt variation, se om det fungerer efter hensigten, og ja vel egentlig bare for hyggens skyld
	#	På forgående side, som er ret så dynamisk og velskrevet hvis jeg selv skal sige det, bliver der defineret en masse variabler, alt efter hvilken
	#	QUERY_STRING man har. Det betyder der er et sæt standard variabler, når man klikker sig ind på en kategori, bliver et nyt sæt variabler sat, og igen
	#	når man klikker sig ind på en service. En af de variabler er navnet på submit knapperne (der er 2), og her under opfanger den egentlig bare, hvilken af
	#	de knapper man har trykket, og udfører en handling derefter.



	#Hvis man har trykket "Opret kategori" knappen
	if(isset($_POST['createCat'])){
		$catName = mysqli_real_escape_string($conn,$_POST['catName']);
		$sql = "INSERT INTO categories (category,services) VALUES ('".$catName."',1)";
		$event = "Kategori: ".$catName." blev oprettet";
		$sess = $catName." oprettet";
	};

	#Hvis man har trykket "Opdater kategori" knappen
	if(isset($_POST['updateCat'])){
		$catName = mysqli_real_escape_string($conn,$_POST['catName']);
		$id = mysqli_real_escape_string($conn,$_GET['id']);
		$sql = "UPDATE categories SET category='".$catName."' WHERE id='".$id."'";
		$event = "Kategori: ".$catName." blev opdateret";
		$sess = $catName." opdateret";

	};

	#Hvis man opretter en service eller opdaterer en service
	if(isset($_POST['addService']) || isset($_POST['updateService'])){
		$head = mysqli_real_escape_string($conn,$_POST['head']);
		$para = mysqli_real_escape_string($conn,$_POST['para']);
		$price = mysqli_real_escape_string($conn,$_POST['price']);
		$id = mysqli_real_escape_string($conn,$_GET['id']);
		$sql = "SELECT category FROM categories WHERE id='".$id."'";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$result = mysqli_fetch_array($query);
		if(isset($_POST['addService'])){
			$sess = $head." tilføjet";
			$event = "Service: ".$head." blev tilføjet til ".$result['category'];
			$sql = "INSERT INTO services (head,para,price,category) VALUES ('".$head."','".$para."','".$price."','".$id."')";
		}else{
			$sess = $head." opdateret";
			$event = "Service: ".$head." blev opdateret i ".$result['category']." kategorien";
			$sid = mysqli_real_escape_string($conn,$_GET['sid']);
			$sql = "UPDATE services SET head='".$head."', para='".$para."', price='".$price."', category='".$id."' WHERE id='".$sid."'";
		};
	};

	$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

	if(mysqli_query($conn,$sql)){
		$_SESSION['serviceSuccess'] = $sess;
		$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','".$event."','".$time."','services','".$uid."')";

	}else{
		$_SESSION['serviceError'] = TRUE;
		$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Ukendt fejl! Indhold blev ikke opdateret','".$time."','services','".$uid."',1)";
	};

	mysqli_query($conn,$sql)or die(mysqli_error($conn));

	if(!isset($sid)){
		header("location:".$_SESSION['last']);
	}else{
		header("location:../../godmode.php?services&kategori=".$id);
	};


?>