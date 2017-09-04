<?php

	#Sikkerhed bl.a.
	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/delMedia.php)','".$time."',1,'sikkerhed')";
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


	#Klargør lidt inputs
	$id = mysqli_real_escape_string($conn,$_GET['id']);
	$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);
	$sql = "SELECT * FROM media WHERE id='".$id."'";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$result = mysqli_fetch_array($query);

	#Definer tilhørende side
	if($result['blog'] == TRUE){
		$rel = "blog";
	};
	if($result['front'] == TRUE){
		$rel = "forsiden";
	};
	if($result['portfolio'] == TRUE){
		$rel = "portfolio";
	};
	if($result['info'] == TRUE){
		$rel = "info";
	};

	#Hvis man vil slette et billede fra karrusellen på forsiden, slet kun billedet (ingen #id mappe) - opret log undervejs
	if($rel == "forsiden"){
		if(unlink("../../".$result['file'])){
			$sql = "DELETE FROM media WHERE front=1 AND id='".$id."'";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Billede karrusel: Medie slettet','".$time."','forsiden','".$uid."')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
		}else{
			$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Billede karrusel: Medie blev ikke slettet','".$time."','forsiden','".$uid."',1)";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
		};

		header("location:".$_SESSION['last']);
		exit;
	};

	#Hvis man sletter fra et projekt, slet kun billede/hentbar fil - opret log undervejs
	if($rel == "portfolio" && !isset($_GET['con'])){
		if(unlink("../../".$result['file'])){

			#Billede eller hentbar fil
			$file = "Billede";
			if($result['dl'] == 1){
				$file = "Hentbar fil";
			};

			#Tjek om det er preview billedet
			if($result['preview'] == 1){
				$pre = true;
			};

			#projektet (til log)
			$sql = "SELECT id FROM portfolio WHERE id='".$result['mid']."'";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$result = mysqli_fetch_array($query);

			#slet fra media db
			$sql = "DELETE FROM media WHERE portfolio=1 AND id='".$id."'";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));

			#opret log
			$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','".$file." slettet fra projekt#".$result['id']."','".$time."','portfolio','".$uid."')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));

			#Sæt nyt preview billede, hvis nødvendigt
			if(isset($pre)){
				$sql = "SELECT id FROM media WHERE portfolio=1 AND mid='".$result['id']."' LIMIT 1";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$result = mysqli_fetch_array($query);
				$sql = "UPDATE media SET preview=1 WHERE id='".$result['id']."'";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			};

		}else{
			$sql = "INSERT INTO events (ip,event,time,rel,uid,danger) VALUES ('".$client."','Filen ".$result['file']." blev ikke slettet','".$time."','forsiden','".$uid."',1)";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
		};

		header("location:".$_SESSION['last']);
		exit;
	};


	#Slet mappen
	$file = "media/".$rel."/".$result['mid'];
	rrmdir("../../".$file);

	#Tjek om mappen stadig eksisterer, og opret log udfra fundet
	if(!is_dir("../../".$file)){
		$sql = "INSERT INTO events (ip,event,time,rel,uid) VALUES ('".$client."','Mappen og dens indhold blev slettet fra ".$file."','".$time."','".$rel."','".$uid."')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		$sql = "DELETE FROM media WHERE id='".$id."'";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
	}else{
		$sql = "INSERT INTO events (ip,event,time,rel,danger,uid) VALUES ('".$client."','Mappen og dens indhold ".$file." blev ikke slettet','".$time."','".$rel."',1,'".$uid."')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		if(isset($_SESSION['createSuccess'])){
			unlink($_SESSION['createSuccess']);
		};
		$_SESSION['createError'] = "Indholdets medie blev ikke slettet ".mysqli_error($conn);
	};

	#Hvis man kommer fra delContent.php dokumentet, rediger lidt i links
	if(isset($_GET['con'])){
		if($rel == "blog"){
			header("location:../../godmode.php?fl&blog");
			exit;
		};
		if($rel == "portfolio"){
			header("location:../../godmode.php?fl&portfolio");
			exit;
		};
	};

	header("location:".$_SESSION['last']);


?>