<?php

	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (forsiden.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
		exit;
	};




	########################
	#	Opdater forsiden
	########################

	#Hent data
	$sql = "SELECT * FROM main WHERE id=1";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$result = mysqli_fetch_array($query);

	#Åben velkomst panel
    $flSearch = strpos($_SERVER['QUERY_STRING'],"fl&");
    $flClear = substr_replace($_SERVER['QUERY_STRING'],'',$flSearch,3);
    $flClear = $_SERVER['PHP_SELF']."?".$flClear;

    #luk velkomst panel
    $fl = $_SERVER['PHP_SELF']."?fl&".$_SERVER['QUERY_STRING'];

	#Skifter panelet, alt an på status
	$panelHead = "Velkomst";
	$panel = "default";
	if(isset($_SESSION['mainError'])){
		$panel = "danger";
		$panelHead = $_SESSION['mainError'];
		unset($_SESSION['mainError']);
	};
	if(isset($_SESSION['mainSuccess'])){
		$panel = "success";
		$panelHead = "Forsiden opdateret";
		unset($_SESSION['mainSuccess']);
	};
	if(isset($_SESSION['guestWarning'])){
		$panelHead = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};

	echo'<script> var Txt = '.$result['txt'].'; </script>';



    #Hvis 'velkomst' panel er åbent
    if(!isset($_GET['fl'])){

		#Wysiwyg editor
		echo'<div class="panel panel-'.$panel.'">';
			echo'<div class="panel-heading">';
				echo'<b>'.$panelHead.'</b>';
				echo'<a href="'.$fl.'" title="Luk panel" role="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></a>';
			echo'</div>';
			echo'<div class="panel-body">';
				echo'<form method="post" action="godmode/php/updateIndex.php">';
					echo'<textarea name="txt" class="form-control" id="summernote" style="margin-bottom:1%;" required>'.$result['txt'].'</textarea>';
					echo'<input type="submit" name="okUpdate" value="Opdater" class="btn btn-primary btn-block">';
				echo'</form>';

			echo'</div>';
		echo'</div>';
	};


	#Hvis velkomst panelet er lukket
	if(isset($_GET['fl'])){

        #Panelet
	    echo'
		<div class="panel panel-'.$panel.'">
			<div class="panel-heading" style="text-align:right; vertical-align:middle;">
				<a href="'.$flClear.'" role="button" class="btn btn-default btn-xs"><span  class="caret" aria-hidden="true"></span></a>
				<b class="pull-left">'.$panelHead.'</b>
			</div>
		</div>';
	};



	#Skifter panelet, alt an på status
	$panelHead = "Billede Karrusel";
	$panel = "default";
	if(isset($_SESSION['createError'])){
		$panel = "danger";
		$panelHead = $_SESSION['createError'];
		unset($_SESSION['createError']);
	};
	if(isset($_SESSION['createSuccess'])){
		$panel = "success";
		$panelHead = $_SESSION['createSuccess'];
		unset($_SESSION['createSuccess']);
	};
	if(isset($_SESSION['guestWarning'])){
		$panelHead = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};




	#Billede karrusellen
	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading"><b>'.$panelHead.'</b></div>';
		echo'<div class="panel-body">';

			#Tilføj medie
			echo'<form method="post" action="godmode/php/addCarousel.php" enctype="multipart/form-data">';
				echo'<div class="row">';
					echo'<div class="col-lg-12">';
						echo'<div class="input-group" style="margin:2% 5% 3% 5%; width:90%;">';
								echo'<input type="file" name="file" class="filestyle" data-buttonText="Tilføj nyt billede" data-buttonName="btn-primary" data-buttonBefore="true">';
								echo'<span class="input-group-btn">';
									echo'<input type="submit" name="okAdd" class="btn btn-primary" type="button" value="Overfør" />';
								echo'</span>';
						echo'</div>';
					echo'</div>';
				echo'</div>';
			echo'</form>';



			#Aktuelle billeder
			echo'<table class="table">';

			#Vis først det primære billede
			$sql = "SELECT * FROM media WHERE front=1 AND preview=1";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$num = mysqli_num_rows($query);
			if($num == true){
				$result = mysqli_fetch_array($query);
				echo'<tr>';
					echo'<td style="width:40%; padding:1% 5% 1% 5%; border:none;">';
						echo'<img src="'.$result['file'].'" class="" style="width:100%;"/>';
					echo'</td>';
					echo'<td style="padding:2% 5% 1% 0; border:none;">';
						echo'
						<div class="has-success">
						  <div class="checkbox">
						    <label>
						      <input type="checkbox" checked disabled>
						      Primær billede
						    </label>
						  </div>
						</div>';

						echo'<input type="text" value="'.$result['file'].'" class="form-control" disabled/>';
						echo'<a onclick="delImgCon('.$result['id'].');" class="btn btn-danger btn-sm" style="margin-top:.5%;" title="Slet billede">Slet</a>';
					echo'</td>';
				echo'</tr>';
			};

			#De resterende billeder (ikke primære)
			$sql = "SELECT * FROM media WHERE front=1 AND preview=0";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));

			#Sørger for der ikke kommer border på det først hentede billede
			$br = 0;
			while($result = mysqli_fetch_array($query)){
				$border = NULL;
				if($br == 0 && $num != true){
					$border = 'border:none;';
					$br++;
				};

				echo'<tr>';
					echo'<td style="width:40%; padding:1% 5% 1% 5%; '.$border.'">';
						echo'<img src="'.$result['file'].'" class="" style="width:100%;"/>';
					echo'</td>';
					echo'<td style="padding:2% 5% 1% 0; '.$border.'">';
						echo'<a href="godmode/php/setPrimary.php?id='.$result['id'].'" style="margin-bottom:.5%;" class="btn btn-primary btn-sm" title="Aktiver som primær">Primær</a>';
						echo'<input type="text" value="'.$result['file'].'" class="form-control" disabled/>';
						echo'<a href="godmode/php/delMedia.php?id='.$result['id'].'" class="btn btn-danger btn-sm" style="margin-top:.5%;" title="Slet billede">Slet</a>';
					echo'</td>';
				echo'</tr>';
			};
			echo'</table>';
		echo'</div>';
	echo'</div>';

?>



<script type="text/javascript">
	//Bekræft når man vil slette et billede
	function delImgCon(id){
		var r = confirm("Er du sikker?");
		if (r == true) {
		    window.location = "godmode/php/delMedia.php?id=" + id;
		};
	};

	//wysiwyg
	$(document).ready(function() {
	    $('#summernote').summernote({
	        height:500,
	        toolbar: [
	            ['insert',['picture','link','video']],
	            ['font style',['fontsize','color','bold','italic','underline','strikethrough']],
	            ['para',['ol','ul','paragraph']],
	            ['misc',['undo','codeview']]
	        ]
	    });
	})
</script>