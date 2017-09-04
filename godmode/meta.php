<?php
	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (meta.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
		exit;
	};


	#Standard værdier
	$sel = "author=1";
	$page = "sloa.dk";
	$mainAct = NULL;
	$blogAct = NULL;
	$infoAct = NULL;
	$contAct = NULL;
	$portAct = NULL;
	$servAct = NULL;
	$cmsAct = NULL;
	$pregAct = NULL;
	$sloaAct = "active";

	#Definer hvilken siden der skal opdateres/ er aktiv
    if(isset($_GET['forsiden'])){
        $sel = "main=1";
        $page = 'forsiden';
        $mainAct = "active";
		$sloaAct = NULL;
    };
    if(isset($_GET['blog'])){
        $sel = "blog=1";
        $page = 'bloggen';
        $blogAct = "active";
		$sloaAct = NULL;
    };
    if(isset($_GET['info'])){
        $sel = "info=1";
        $page = 'info siden';
        $infoAct = "active";
		$sloaAct = NULL;
    };
    if(isset($_GET['kontakt'])){
        $sel = "contact=1";
        $page = 'kontakt siden';
        $contAct = "active";
		$sloaAct = NULL;
    };
    if(isset($_GET['portfolio'])){
        $sel = "portfolio=1";
        $page = 'portfolio siden';
        $portAct = "active";
		$sloaAct = NULL;
    };
    if(isset($_GET['services'])){
        $sel = "services=1";
        $page = 'service siden';
        $servAct = "active";
		$sloaAct = NULL;
    };
    if(isset($_GET['login'])){
        $sel = "pregodmode=1";
        $page = 'login siden';
        $pregAct = "active";
		$sloaAct = NULL;
    };
    if(isset($_GET['cms'])){
        $sel = "godmode=1";
        $page = 'CMS';
        $cmsAct = "active";
		$sloaAct = NULL;
    };

    #Hent aktuelle data
    $sql = "SELECT * FROM meta WHERE ".$sel."";
    $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
    $result = mysqli_fetch_array($query);

    #Array til follow muligheder (bliver hentet i en while)- til at vælge den ænskede værdi
    $follow = array("index, follow", "index, nofollow", "noindex, follow", "noindex, nofollow");
    $br = 0;

	echo'<nav class="navbar navbar-default" style="margin-top:-.5%;">';
		echo'<div class="container-fluid">';

		    #Top menuen / knapper
		    echo'
		    <ul class="nav navbar-nav">
			  <li class="'.$sloaAct.'"><a href="godmode.php?meta">sloa.dk</a></li>
			  <li class="'.$mainAct.'"><a href="godmode.php?meta&forsiden">Forsiden</a></li>
			  <li class="'.$blogAct.'"><a href="godmode.php?meta&blog">Blog</a></li>
			  <li class="'.$infoAct.'"><a href="godmode.php?meta&info">Info</a></li>
			  <li class="'.$portAct.'"><a href="godmode.php?meta&portfolio">Portfolio</a></li>
			  <li class="'.$servAct.'"><a href="godmode.php?meta&services">Services</a></li>
			  <li class="'.$contAct.'"><a href="godmode.php?meta&kontakt">Kontakt</a></li>
			  <li class="'.$pregAct.'"><a href="godmode.php?meta&login">Login</a></li>
			  <li class="'.$cmsAct.'"><a href="godmode.php?meta&cms">CMS</a></li>
			</ul>';
		echo'</div>';
	echo'</nav>';

	$note = 'Metadata for '.$page;
	$panel = "default";
	if(isset($_SESSION['metaSuccess'])){
		$note = "Metadata opdateret for ".$page;
		$panel = "success";
		unset($_SESSION['metaSuccess']);
	};
	if(isset($_SESSION['metaError'])){
		$note = "Der opstod en fejl!";
		$panel = "danger";
		unset($_SESSION['metaError']);
	};
	if(isset($_SESSION['guestWarning'])){
		$note = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};

	#Meta formula
	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading"><b>'.$note.'</b></div>';
		echo'<div class="panel-body">';
			echo'<form method="post" action="godmode/php/updateMeta.php?rel='.$page.'">';
			#Hvis det kun er udgiver der er aktiv
		    if($page == "sloa.dk"){
		    	echo'<label for="description" style="margin-top:.8%;">Sidens udgiver</label>';
		    	echo'<input type="text" name="description" id="description" class="form-control" value="'.$result['description'].'" />';
		    #Hvis det ikke er udgiver der er aktiv
		    }else{
				echo'<label style="margin-top:.8%;" for="title">Sidens titel</label>';
				echo'<input type="text" class="form-control" id="title" name="title" value="'.$result['title'].'" required>';
				echo'<label style="margin-top:.8%;" for="description">Sidens beskrivelse</label>';
				echo'<input type="text" class="form-control" id="description" name="description" value="'.$result['description'].'" required>';
				echo'<label style="margin-top:.8%;" for="keywords">Sidens nøgleord</label>';
				echo'<input type="text" class="form-control" id="keywords" name="keywords" value="'.$result['keywords'].'" required>';
				echo'<label for="follow"  style="margin-top:.8%;">Søgemaskiners robot indeksering</label>';
				echo'<select class="form-control" id="follow" name="follow">';
				while($br <= 3){
					$selected = NULL;
					if($result['follow'] == $follow[$br]){
						$selected = "selected";
					};
					echo'<option '.$selected.'>'.$follow[$br].'</option>';
					$br++;
				};
				echo'</select>';
			};
			echo '<input type="submit" name="okMeta" class="btn btn-primary btn-sm" value="Opdater" style="margin-top:.5%;"/>';
			echo'</form>';
		echo'</div>';
	echo'</div>';

?>