<?php
	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (footer.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
	};


	#Skifter panelets top, hvis en profil er oprettet eller en fejl er sket
	$panel = "default";
	$panelTxt = "Social medie & Kontakt oplysninger";
	if(isset($_SESSION['conSuccess'])){
		$panel = "success";
		$panelTxt = "Kontakt oplysningerne blev opdateret";
		unset($_SESSION['conSuccess']);
	};

	if(isset($_SESSION['conError'])){
		$panel = "danger";
		$panelTxt = $_SESSION['conError'];
		unset($_SESSION['conError']);
	};

	if(isset($_SESSION['guestWarning'])){
		$panelTxt = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};


	#Social medie (SM)
	$head = "Opret social medie";
	$buttonVal = "Opret";
	$buttonName = "addSocial";
	$link = NULL;
	$title = NULL;
	$close = NULL;

	#Hvis man redigere end allerede oprettet SM
	if(isset($_GET['rediger'])){
		$id = mysqli_real_escape_string($conn,$_GET['rediger']);
		$sql = "SELECT link,link_title,active FROM socialmedia WHERE id='".$id."'";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
		if(mysqli_num_rows($query) == true){
			$confirm = true;
			$head = "Rediger social medie";
			$buttonVal = "Opdater";
			$buttonName = "updateSocial";
			$result = mysqli_fetch_array($query);
			$link = $result['link'];
			$title = $result['link_title'];
			$check = "checked";
			if($result['active'] == 0){
				$check = NULL;
			};
			$close = '<a href="'.$_SERVER['PHP_SELF'].'?footer" style="margin-top:.5%;" title="Tilbage til opret" role="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></a>';
		};
	};

	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading"><b>'.$panelTxt.'</b></div>';
		echo'<div class="panel-body">';

			#Form til opret/rediger SM
			echo'<div class="pull-left" style="width:40%; margin:0 5% 0 5%;">';
				echo'<b class="small text-muted">'.$head.'</b>'.$close.'<hr />';
				echo'<form method="post" action="godmode/php/updateFooter.php" enctype="multipart/form-data">';
					echo'<label for="title">Link titel</label>';
					echo'<input type="text" value="'.$link.'" id="title" name="title" class="form-control" required/>';
					echo'<label for="link" style="margin-top:1%;">Link</label>';
					echo'<input type="text" value="'.$title.'" id="link" name="link" class="form-control" style="margin-bottom:1%;" required/>';
					echo'<input type="file" name="file" class="filestyle" data-buttonText=" Ikon" data-buttonName="btn-primary" data-buttonBefore="true">';
					if(isset($confirm)){
						echo'<label style="margin-top:1%;"><input type="checkbox" '.$check.'/> Aktiv</label>';
						echo'<br />';
					};
					echo'<input type="submit" name="'.$buttonName.'" class="btn btn-primary" style="margin:1% 0 8%;" value="'.$buttonVal.'"/>';
				echo'</form>';


				#Hent og vis de allerede oprettede SM
				echo'<b class="small text-muted">Aktuelle sociale medier</b><hr />';
				$sql = "SELECT * FROM socialmedia ORDER BY id ASC";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				while($result = mysqli_fetch_array($query)){
					$txtState = "text-success";
					$active = "Aktiv";
					if($result['active'] == 0){
						$txtState = "text-danger";
						$active = "Inaktiv";
					};
					echo'<div style="float:left; width:40%; background-color:#F8F8F8; padding:2%; margin:5%; 2.5% 0 2.5%">';
						echo'<b class="small '.$txtState.' pull-right">'.$active.'</b>';
						echo'<a href="'.$_SERVER['PHP_SELF'].'?footer&rediger='.$result['id'].'" class="small" title="Rediger"><b>';
						echo'<img src="'.$result['icon'].'" style="width:25px; clear:both;"/>';
						echo'<br />';
						echo $result['link'];
						echo'<br />';
						echo $result['link_title'];
						echo'</b></a>';
					echo'</div>';
				};
			echo'</div>';

			#Kontakt oplysninger
			$sql = "SELECT * FROM footer WHERE id=1";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$result = mysqli_fetch_array($query);
			echo'<div class="pull-right" style="width:40%; margin:0 5% 0 5%">';
				echo'<b class="small text-muted">Kontakt oplysninger</b><hr />';
				echo'<form method="post" action="godmode/php/updateFooter.php">';
					echo'<label for="name">Navn</label>';
					echo'<input type="text" id="name" value="'.$result['name'].'" name="name" class="form-control" required/>';
					echo'<label style="margin-top:1%;" for="c ty">Geografisk</label>';
					echo'<input type="text" id="city" value="'.$result['adress'].'" name="city" class="form-control" required/>';
					echo'<label style="margin-top:1%;" for="phone">Telefon</label>';
					echo'<input type="text" id="phone" value="'.$result['phone'].'" name="phone" class="form-control" required/>';
					echo'<label style="margin-top:1%;" for="mail">E-mail</label>';
					echo'<input type="text" id="mail" value="'.$result['mail'].'" name="mail" class="form-control" required/>';
					echo'<input type="submit" name="okCon" class="btn btn-primary" style="margin-top:1%;" value="Opdater"/>';
				echo'</form>';
			echo'</div>';
		echo'</div>';
	echo'</div>';

?>