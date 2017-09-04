<?php

	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	$client = mysqli_real_escape_string($conn,clientIP());
	$time = mysqli_real_escape_string($conn,timeMe());

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/addCarousel.php)','".$time."',1,'sikkerhed')";
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


	if(isset($_POST['okAdd'])){

		#Billede upload
		if($_FILES['file']['size'] > 0){

			#definere lidt forskelligt
			$imgDanger = 0;
			$filetypes = array("jpg","jpeg","pjpeg","gif","png");
			$dir = "../../media/forsiden/";
			$file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			$mtime = microtime();
			$locktime = substr(md5($mtime),0,6);
			$aa = $dir.$locktime.".".$file_ext;
			$cleanPath = substr($aa,6);
			$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);

			#Skal være billede og må ikke overskride ....190MB - hah! Server bestemt ;)
			if(!in_array($file_ext,$filetypes)){
				$_SESSION['createError'] = "Ikke accepteret filtype";
				$imgEvent = "Billede karrusel: Ikke accepteret filtype";
				$imgDanger = 1;
			};

			if($_FILES['file']['size'] >= 199229000){
				$_SESSION['createError'] = "Overskrider 190MB ('ish)";
				$imgEvent = "Billede karrusel: Større end 190MB";
				$imgDanger = 1;
			};


			#Smid tilbage hvis fejl
			if($imgDanger == true){
				$sql = "INSERT INTO events (ip,event,time,uid,rel,danger) VALUES ('".$client."','".$imgEvent."','".$time."','".$uid."','forsiden',".$imgDanger.")";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				header("location:".$_SESSION['last']);
				exit;
			};

			#Forsøg at overfør fil
			if(move_uploaded_file($_FILES['file']['tmp_name'],$aa)){
				$imgEvent = "Fil overført til ".$cleanPath;
				$sql = "INSERT INTO media (file,front) VALUES ('".$cleanPath."',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$_SESSION['createSuccess'];
			}else{
				$_SESSION['createError'] = "Ukendt fejl opstået ved overførsel #".$_FILES['file']['error'];
				$imgEvent = "Billede karrusel: Fejl ved overførsel # ".$_FILES['file']['error'];
				$imgDanger = 1;
			};

			#Opret log
			$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$client."','".$imgEvent."','".$time."','".$uid."',".$imgDanger.",'forsiden')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));

		};
	};


	header("location:".$_SESSION['last']);


?>