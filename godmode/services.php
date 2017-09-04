<?php
	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (services.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
	};


	# Kategorier	#####


	#Standard værdier
	$sql = "SELECT * FROM categories WHERE services=1 ORDER BY id ASC";
	$val = NULL;
	$para = NULL;
	$price = NULL;
	$catBtnVal = "Opret";
	$catBtnName = "createCat";
	$id = NULL;
	$delTitle = "Slet kategori og dens indhold";
	$delFunc = "delCat";
	#While lykke
	$tdHead = "Kategorier";
	$tdPara = "Produkter";
	$href = $_SERVER['PHP_SELF']."?services&kategori=";
	$aTitle = "Åben kategori";
	$track = '<span class="small text-muted"><b>Kategorier</b></span>';


	#Hvis man åbner en kategori
	$cat = NULL;
	if(isset($_GET['kategori'])){
		$id = mysqli_real_escape_string($conn,$_GET['kategori']);
		$chkSql = "SELECT id,category FROM categories WHERE id='".$id."'";
		$chkQuery = mysqli_query($conn,$chkSql);
		#Hvis kategorien findes
		if(mysqli_num_rows($chkQuery) == true){
			$chkResult = mysqli_fetch_array($chkQuery);
			$sql = "SELECT * FROM services WHERE category='".$id."'";
			$cat = $id;
			$val = $chkResult['category'];
			$label = "Overskrift";
			$btnName = "addService";
			$btnVal = "Opret";
			$catBtnVal = "Opdater";
			$catBtnName = "updateCat";
			$id = "?id=".$chkResult['id'];
			$delTitle = "Slet service";
			$delFunc = "delServ";
			#While lykken
			$tdHead = "Service";
			$tdPara = "Pris";
			$href = $_SERVER['PHP_SELF']."?services&kategori=".$cat."&service=";
			$aTitle = "Rediger service";
			#breadcrumbs
			$bread = $val;
			$track = '<span class="small text-muted"><b>'.$val.'</b></span>';
			$track = '<a href="'.$_SERVER['PHP_SELF'].'?services" class="small"><b>Kategorier</b></a> / '.$track;
		};
	};



	#Hvis man åbner en service
	$serv = NULL;
	if(isset($_GET['service']) && $cat == true){
		$sid = mysqli_real_escape_string($conn,$_GET['service']);
		$chkSql = "SELECT * FROM services WHERE id='".$sid."'";
		$chkQuery = mysqli_query($conn,$chkSql);
		if(mysqli_num_rows($chkQuery) == true){
			$chkResult = mysqli_fetch_array($chkQuery);
			$serv = $sid;
			$val = $chkResult['head'];
			$btnVal = "Opdater";
			$btnName = "updateService";
			$para = $chkResult['para'];
			$price = $chkResult['price'];
			$id = $id."&sid=".$chkResult['id'];
			#breadcrumbs
			$track = '<span class="small text-muted"><b>'.$val.'</b></span>';
			$track = '<a href="'.$_SERVER['PHP_SELF'].'?services&kategori='.$cat.'" class="small"><b>'.$bread.'</b></a> / '.$track;
			$track = '<a href="'.$_SERVER['PHP_SELF'].'?services" class="small"><b>Kategorier</b></a> / '.$track;
		};
	};


	#Udfør hvad end handling er aktuelt
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$num = mysqli_num_rows($query);

	$br = 0;

	#Skifter panelet, alt an på status
	$panelHead = "Kategorier og Services";
	$panel = "default";
	if(isset($_SESSION['serviceError'])){
		$panel = "danger";
		$panelHead = "Der opstod en fejl!";
		unset($_SESSION['serviceError']);
	};
	if(isset($_SESSION['serviceSuccess'])){
		$panel = "success";
		$panelHead = $_SESSION['serviceSuccess'];
		unset($_SESSION['serviceSuccess']);
	};
	if(isset($_SESSION['guestWarning'])){
		$panelHead = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};


	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading"><b>'.$panelHead.'</b></div>';
		echo'<div class="panel-body">';

			#breadcrumbs
			echo'<div style="margin:2% 0 2% 10%;">';
				echo $track;
			echo'</div>';

			# Tilføj(ediger kategori og tilføj/rediger service
			echo'<form method="post" style="margin:1% 0 4% 10%; width:80%;" action="godmode/php/updateServices.php'.$id.'">';

			#Vis kategori feltet, med mindre en service er åben
			if(!isset($serv)){
				if(!isset($cat)){
					echo'<label>Opret en kategori</label>';
					echo'<hr style="margin:0 0 2%;"/>';
					$val = NULL;
				};
				echo'<div class="input-group">';
					echo'<input type="text" id="catName" class="form-control" name="catName" value="'.$val.'" placeholder="Kategori navn" required />';
					echo'<span class="input-group-btn">';
						echo'<input type="submit" name="'.$catBtnName.'" value="'.$catBtnVal.'" class="btn btn-primary" />';
					echo'</span>';
				echo'</div>';

			};

			#Hvis man har klikket sig videre ind på en kategori
			if(isset($cat)){
				if(!isset($serv)){
					echo'<label style="margin-top:4%;">Opret et produkt i '.$val.'</label>';
					echo'<hr style="margin:0 0 2%;"/>';
					$val = NULL;
				};
				echo'<label class="small" for="head">'.$label.'</label>';
				echo'<input type="text" id="head" class="form-control" name="head" value="'.$val.'" required/>';
				echo'<label class="small" style="margin-top:1%;" for="para">Info</label>';
				echo'<input type="text" id="para" name="para" class="form-control" value="'.$para.'"/>';
				echo'<label class="small" style="margin-top:1%;" for="price">Pris</label>';
				echo'<br />';
				echo'<input type="text" style="width:10%; float:left;" id="price" name="price" class="form-control" value="'.$price.'" required/>';
				echo'<input type="submit" style="margin-left:.5%;" name="'.$btnName.'" value="'.$btnVal.'" class="btn btn-primary">';
			};

			echo'</form>';


			#Henter kategorier/services
			if($num == true){
				echo'<table class="table">';
					echo'<thead class="small text-muted" style="font-weight:bold;">';
						echo'<tr>';
							echo'<td style="width:1px;"></td>';
							echo'<td style="width:1px;">#</td>';
							echo'<td>'.$tdHead.'</td>';
							echo'<td class="text-right">'.$tdPara.'</td>';
						echo'</tr>';
					echo'</thead>';
					echo'<tbody>';
					#Inholdet bliver hentet dynamisk
						while($result = mysqli_fetch_array($query)){
							#Hvis man ikke er i en kategori/service
							if(!isset($cat) && !isset($serv)){
								$productSql = "SELECT id FROM services WHERE category='".$result['id']."'";
								$productQuery = mysqli_query($conn,$productSql)or die(mysqli_error($conn));
								$paraRel = mysqli_num_rows($productQuery);
								$aTxt = $result['category'];
							};
							#Hvis man åbner en kategori
							if(isset($cat)){
								$paraRel = $result['price'];
								$aTxt = $result['head'];
							};
							$br++;
							echo'<tr>';
								echo'<td><a onClick="'.$delFunc.'('.$result['id'].')" class="btn btn-danger btn-xs" title="'.$delTitle.'">Slet</a></td>';
								echo'<td>'.$br.'</td>';
								echo'<td><a href="'.$href.$result['id'].'" title="'.$aTitle.'" class="small"><b>'.$aTxt.'</b></td>';
								echo'<td class="text-right">'.$paraRel.'</td>';
							echo'</tr>';
						};
					echo'</tbody>';
				echo'</table>';
			};

		echo'</div>';
	echo'</div>';

?>


	<script>
	//Bekræft når man vil slette en kategori
	function delCat(id){
		var x = confirm("Er du sikker?");
		if (x == true) {
		    window.location = "godmode/php/delContent.php?rel=service_category&id=" + id;
		};
	};

	//Bekræft når man vil slette en service
	function delServ(id){
		var r = confirm("Er du sikker?");
		if (r == true) {
		    window.location = "godmode/php/delContent.php?rel=services&id=" + id;
		};
	};
	</script>