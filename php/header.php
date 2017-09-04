<?php

	#Farve på knappen, når man er på en specifik side
	$style_kontakt = NULL;
	$style_services = NULL;
	$style_portfolio = NULL;
	$style_info = NULL;
	$style_blog = NULL;
	#Til administrator panel (sender dig til aktuel CMS side)
	$adm = "?forsiden";

	if($_SERVER['PHP_SELF'] == "/kontakt.php"){
		$style_kontakt = "color:#232224;";
		$adm = "?kontakt";
	};

	if($_SERVER['PHP_SELF'] == "/services.php"){
		$style_services = "color:#232224;";
		$adm = "?services";
	};

	if($_SERVER['PHP_SELF'] == "/portfolio.php"){
		$style_portfolio = "color:#232224;";
		$adm = "?portfolio";
	};

	if($_SERVER['PHP_SELF'] == "/info.php"){
		$style_info = "color:#232224;";
		$adm = "?info";
	};

	if($_SERVER['PHP_SELF'] == "/blog.php"){
		$style_blog = "color:#232224;";
		$adm = "?blog";
	};


	#Baggrunde
	echo'
		<div class="bg0"></div>
		<div class="bg1"></div>
		<div class="bg2"></div>';


	#Administrator bar
	if(isset($_SESSION['sloaLogged'])){
		echo'<div class="container-fluid" style="margin-bottom:1.5%;">';
			echo'<div class="row loggedBar">';
				echo'<div class="col-md-12">
						<small>Logget ind som <b>'.$user['uname'].'</b></small>
						<a href="php/logout.php" class="btn btn-xs btn-default pull-right" style="margin-left:.2%;" role="button" title="Log ud"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>
						<a href="godmode.php'.$adm.'" class="btn btn-xs btn-default pull-right" role="button" title="Administrator panel"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></a>
					</div>';
			echo'</div>';
		echo'</div>';
	};


	echo'<div class="container-fluid">';
		echo'<div class="row">';
			echo'<div class="col-md-3"></div>';
			echo'<div class="col-md-6 headerCon">';
				
				#Menupunkter
				echo'<div style="border-bottom:1px solid lightgrey; margin:1% 0 1% 0;">';
					echo'<a href="/" class="sloaLogo">sloa.dk</a>';
					echo'&nbsp;&nbsp;&nbsp;<span class="alpha headline">| Alpha</span>';
						echo'<a href="kontakt.php" style="'.$style_kontakt.'" class="mainButton pull-right"><i>kontakt</i></a>';
						echo'<a href="services.php" style="'.$style_services.'" class="mainButton pull-right"><i>services</i></a>';
						echo'<a href="portfolio.php" style="'.$style_portfolio.'" class="mainButton pull-right"><i>portfolio</i></a>';
						echo'<a href="info.php" style="'.$style_info.'" class="mainButton pull-right"><i>info</i></a>';
						echo'<a href="blog.php" style="'.$style_blog.'" class="mainButton pull-right"><i>blog</i></a>';
				echo'</div>';
			echo'</div>';
			echo'</div>';
		echo'</div>';
	echo'</div>';	


	echo'<div class="container-fluid">';
		echo'<div class="row">';
			echo'<div class="col-md-3"></div>';
			echo'<div class="col-md-6" style="border-radius:4px; background-color:whitesmoke; padding:2% 2% 3% 2%; margin:1.5% 0 2% 0;">';	

?>