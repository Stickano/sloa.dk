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


	if(isset($_POST['okMeta'])){

		#Fastsæt hvilken database der skal opdateres
		if($_GET['rel'] == "forsiden"){
			$rel = "main=1";
		};
		if($_GET['rel'] == "info siden"){
			$rel = "info=1";
		};
		if($_GET['rel'] == "bloggen"){
			$rel = "blog=1";
		};
		if($_GET['rel'] == "service siden"){
			$rel = "services=1";
		};
		if($_GET['rel'] == "kontakt siden"){
			$rel = "contact=1";
		};
		if($_GET['rel'] == "portfolio siden"){
			$rel = "portfolio=1";
		};
		if($_GET['rel'] == "sloa.dk"){
			$rel = "author=1";
		};
		if($_GET['rel'] == "login siden"){
			$rel = "pregodmode=1";
		};
		if($_GET['rel'] == "CMS"){
			$rel = "godmode=1";
		};


		#Hvis en database ikke kunne fastslås (burde ikke kunne ske), smid tilbage
		if(!isset($rel)){
			header("location".$_SESSION['last']);

		#Opdater den valgte meta database
		}else{

			$eventRel = mysqli_real_escape_string($conn,$_GET['rel']);
			$sloaLogged = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);


			#Ideen kører på at opdatere den valgte kategori (frem for at oprette), så med mindre kategori findes fungere ideen ikke. Her sørger jeg lige for, at kategorien er oprettet.
			$sql = "SELECT * FROM meta WHERE ".$rel."";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			if(mysqli_num_rows($query) != true){
			    $flSearch = strpos($rel,"=1");
			    $flClear = substr_replace($rel,'',$flSearch,2);
				$sql = "INSERT INTO meta (title,".$flClear.") VALUES ('sloa.dk',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			};


			#Klargør inputs
			$description = mysqli_real_escape_string($conn,$_POST['description']);
			#Til udgiver
			$title = NULL;
			$keywords = NULL;
			$follow = NULL;
			#Hvis det ikke er udgiver
			if($rel != "sloa.dk"){
				$title = mysqli_real_escape_string($conn,$_POST['title']);
				$keywords = mysqli_real_escape_string($conn,$_POST['keywords']);
				$follow = mysqli_real_escape_string($conn,$_POST['follow']);
			};
			$sql = "UPDATE meta SET title='".$title."', description='".$description."', keywords='".$keywords."', follow='".$follow."' WHERE ".$rel."";

			#Forsøg at opdater og opret log
			if(mysqli_query($conn,$sql)){
				$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Metadata opdateret for ".$eventRel."','".$time."','meta','".$sloaLogged."')";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$_SESSION['metaSuccess'] = true;
			}else{
				$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Metadataen blev ikke opdateret for ".$eventRel."','".$time."','meta','".$sloaLogged."',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$_SESSION['metaError'] = true;
			};

			header("location:".$_SESSION['last']);
		};

	};


?>