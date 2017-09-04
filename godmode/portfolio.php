<?php
	@session_start();

	#sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (portfolio.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
	};


	#Skifter panelet, alt an på status
	$panelHead = "Opret projekt";
	$panel = "default";
	if(isset($_SESSION['createSuccess'])){
		$panel = "success";
		$panelHead = $_SESSION['createSuccess'];
		unset($_SESSION['createSuccess']);
	};
	if(isset($_SESSION['createError'])){
		$panel = "danger";
		$panelHead = $_SESSION['createError'];
		unset($_SESSION['createError']);
	};
	if(isset($_SESSION['guestWarning'])){
		$panelHead = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};

	#Hvis opret projekt panelet er åbnet
	if(!isset($_GET['fl'])){
		$headline = NULL;
		$txt = NULL;
		$reference = NULL;
		$edit = NULL;
		$dlTxt = "Hentbar indhold";
		$subVal = "Opret";
		$editLess = 0;

		#Hvis man redigere i et projekt - sæt værdier
		if(isset($_GET['rediger'])){
			$panelHead = "Rediger projekt";
			$dlTxt = "Overskriv hentbar indhold";
			$id = mysqli_real_escape_string($conn,$_GET['rediger']);
			$sql = "SELECT * FROM portfolio WHERE id='".$id."'";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$result = mysqli_fetch_array($query);
			$txt = $result['txt'];
			$headline = $result['headline'];
			$reference = $result['reference'];
			$edit = $result['id'];
			$subVal = "Opdater projekt";
			$mSql = "SELECT id,file,preview FROM media WHERE portfolio=1 AND dl=0 AND mid='".$edit."'";
			$mQuery = mysqli_query($conn,$mSql)or die(mysqli_error($conn));
			$num = mysqli_num_rows($mQuery);
			$dlSql = "SELECT id,file FROM media WHERE portfolio=1 AND dl=1 AND mid='".$edit."'";
			$dlQuery = mysqli_query($conn,$dlSql)or die(mysqli_error($conn));
			$dlNum = mysqli_num_rows($dlQuery);
			if(!isset($_GET['mm'])){
				$editLess = 1;
			};

			#lukker for tilføjelse af flere billeder
			$idLen = 3;
	        $flSearch = strpos($_SERVER['QUERY_STRING'],"&mm");
	        $flClear = substr_replace($_SERVER['QUERY_STRING'],'',$flSearch,$idLen);
	        $flClear = $_SERVER['PHP_SELF']."?".$flClear;
		};

		#Hvis man lukker panelet
		$page = NULL;
		if(isset($_GET['side'])){
			$page = "&side=".$_GET['side'];
		};
		$close = "?fl&portfolio".$page;

	#Panelet (HTML)
	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading">';
			echo'<b>'.$panelHead.'</b>';
			echo'<a href="'.$_SERVER['PHP_SELF'].$close.'" type="button" class="close" role="button" aria-label="Close"><span aria-hidden="true">&times;</span></a>';
		echo'</div>';

		echo'<div class="panel-body">';

			echo'<form method="post" style="margin-top:.6%;" action="godmode/php/createProject.php" enctype="multipart/form-data">';
			echo'<input type="hidden" name="edit" value="'.$edit.'"/>';

			#Venstre side af kassen (medie/billeder)
			echo'<div class="pull-left" style="width:50%; padding:1%;">';

			#medie muligheder - Hvis man opretter (eller tilføjer flere billeder under redigering)
			if($editLess == 0){

				#lille preview besked - skjul hvis man redigere
				if(!isset($edit)){
					echo'<div style="width:100; text-align:right;"><small><b>Preview</b></small></div>';
				#Vis hvor mange billeder projektet har, hvis man redigere
				}else{
					echo'<div style="border-bottom:1px solid lightgrey; width:100%; margin:1% 0 2% 0;">';
						echo'<small class="text-muted"><b>Billeder tilknyttet projektet : <span style="color:blue;">'.$num.'</span></b></small>';
						#luk for tilføjelse af flere billeder
						echo'<a href="'.$flClear.'" title="Tilbage til billeder" role="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></a>';

					echo'</div>';
				};

				#Billede (upload-bokse)
				$br = 1;
				while($br <= 5){

					#tekst til fil-uploads boksene - samt hvilke funktioner der skal køre
					$checked = NULL;
					$nextBox = $br+1;
					$imgBox = "imgBox".$nextBox."();";
					$radio = "radio".$br."();";
					$display = "visibility:hidden; display:none;";
					$required = NULL;
					if($br == 1){
						$label = "Tilføj billeder";
						$checked = "checked";
						$radio = NULL;
						$display = NULL;
						$required = "required";
					}elseif($br == 2){
						$label = "Indsæt et mere!";
					}elseif($br == 3){
						$label = "Et mere!";
					}elseif($br == 4){
						$label = "Du er der næsten!";
					}else{
						$label = "Et sidste billede!";
						$imgBox = NULL;
					};

					#slå preview muligheden fra hvis man redigere
					if(isset($edit)){
						$radio = NULL;
					};

					#HTML'en (upload-boksene, billedetekst og preview radio-knap)
					echo'<div style="'.$display.' width:100%;" id="imgBox'.$br.'">
							<div style="width:92%; float:left;">

								<label for="file">'.$label.'</label>
								<input type="file" name="file'.$br.'" onChange="smallTxt'.$br.'(); '.$imgBox.' '.$radio.'" id="imgTmp'.$br.'" class="filestyle" data-buttonText="Indsæt billede" data-buttonName="btn-primary" data-buttonBefore="true" '.$required.'>

								<input type="text" style="margin-top:1%; margin-bottom:2%; visibility:hidden; display:none;" id="imgTxt'.$br.'" name="fileTxt'.$br.'" placeholder="Billede beskrivelse" class="form-control">

							</div>';
						#Preview (ikke hvis man redigere)
						if(!isset($edit)){
						echo'
							<div style="text-align:center; margin-top:4%; float:right; width:6%;">
								<div style="'.$display.'"  id="radio'.$br.'">
									<input type="radio" name="primary" value="'.$br.'" '.$checked.'/>
								</div>
							</div>';
						};

					echo'</div>';

					$br++;
				};

			#Medie muligheder, hvis man redigere
			}else{

				#Tilføj flere billeder (knap)
				echo'<div style="border-bottom:1px solid lightgrey; width:100%; padding:2%;">';
				echo'<a href="'.$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'].'&mm" class="btn btn-primary btn-sm btn-block" role="button">Tilføj flere billeder</a>';
				echo'</div>';

				#Vis den hentbare fil, hvis nogen
				if($dlNum == true){
					$dlResult = mysqli_fetch_array($dlQuery);
					echo'<div style="margin-top:2%;"><small><b>Hentbar fil</b></small></div>';

					echo'<div style="border-bottom:1px solid lightgrey; width:100%; padding:0 2%; float:left; margin-bottom:2%;">';

						echo'<div class="pull-left" style="width:20%; padding:1%;">';
							echo'<img src="media/zip.png" style="width:100%;"/>';
						echo'</div>';

						echo'<div style="width:80%; padding:4% 1% 2% 1%; float:right;">';
							echo'<input type="text" class="form-control" value="'.$dlResult['file'].'" disabled />';
			    			echo'<a onClick="delCon('.$dlResult['id'].');" title="Slet download" class="btn btn-danger btn-sm marginTop2" role="button"><b>Slet</b></a>';
						echo'</div>';

					echo'</div>';
				};


				#Billeder
				echo'<div style="margin-top:2%; clear:both;"><small><b>Billeder</b></small></div>';
				$br = 0;
				echo'<table class="table" style="width:100%;">';
				while($mResult = mysqli_fetch_array($mQuery)){

					#Ingen border på det øverste projekt
					$border = NULL;
					if($br == 0){
						$border = "border:none;";
						$br++;
					};

					echo'<tr>';
					echo'<td style="width:20%; padding:2%; vertical-align:middle; '.$border.'">';
						echo'<img src="'.$mResult['file'].'" style="width:100%;"/>';
					echo'</td>';

					echo'<td style="width:80%; padding:2%; vertical-align:middle; '.$border.'">';
						echo'<input type="text" class="form-control" value="'.$mResult['file'].'" disabled />';
		    			echo'<a onClick="delCon('.$mResult['id'].');" title="Slet download" class="btn btn-danger btn-sm marginTop2" role="button"><b>Slet</b></a>';
		    			#Preview mulighed
		    			if($mResult['preview'] == 1){
		    				echo'<button type="button" style="margin-left:1%;" class="btn btn-default btn-sm marginTop2" disabled="disabled">Preview</button>';
		    			}else{
		    				echo'<a href="godmode/php/setPrimary.php?id='.$mResult['id'].'" class="btn btn-primary btn-sm marginTop2" style="margin-left:1%;" title="Sæt som preview billede" role="button">Preview</a>';
		    			};
		    		echo'</td>';
		    		echo'</tr>';
				};
				echo'</table>';


			};#Slut for venstre side af oprettelse/rediger boks;

			echo'</div>';


			#Højre side af kassen (tekst)
			echo'<div class="pull-right" style="width:50%; padding:1%;">';
				echo'
					<label for="headline">Overskrift</label>
					<input type="text" name="headline" style="margin-bottom:2%;" value="'.$headline.'" class="form-control" required/>
					<input type="hidden" name="edit" value="'.$edit.'"/>

					<label for="category">Kategory</label>
					<select name="category" class="form-control" style="margin-bottom:2%;">';
						$br = 1;
						while($br <= 3){
							#Kategori valg
							if($br == 1){
								$optVal = "Web";
							}elseif($br == 2){
								$optVal = "Design";
							}else{
								$optVal = "Andet";
							};
							#Hvis man redigere, tjek den rigtige kategori
							$checked = NULL;
							if(isset($edit)){
								if($result['category'] == $br){
									$checked = "selected";
								};
							};
							echo'<option value="'.$br.'" '.$checked.'>'.$optVal.'</option>';
							$br++;
						};

				echo'
					</select>

					<label for="reference">Referer til</label>
					<input type="text" name="reference" style="margin-bottom:2%;" value="'.$reference.'" class="form-control"/>

					<label for="download">'.$dlTxt.'</label>
					<input type="file" name="download" class="filestyle" data-buttonText=".zip fil" data-buttonName="btn-primary" data-buttonBefore="true">

					<label for="txt" style="margin-top:2%;">Info</label>';
					//wysiwyg
					echo'<textarea name="txt" class="form-control" id="summernote" style="margin-bottom:1%;" required>'.$txt.'</textarea>';
			echo'</div>';

			#slet mulighed, hvis man redigere
			if($editLess == 1){
				echo'<a onclick="delCont('.$edit.')" style="margin-top:2%; clear:both;" class="btn btn-danger btn-block">Slet projekt</a>';
			};

			echo'
			<input type="submit" name="okCreate" class="btn btn-primary btn-block" style="margin-top:1%;" value="'.$subVal.'">
		</form>';

	echo'
		</div>
	</div>';


	#Hvis opret projekt panelet er lukket
	}else{

		#Åben opret panel
	    $flSearch = strpos($_SERVER['QUERY_STRING'],"fl&");
	    $flClear = substr_replace($_SERVER['QUERY_STRING'],'',$flSearch,3);
	    $flClear = $_SERVER['PHP_SELF']."?".$flClear;

        #Panelet
	    echo'
		<div class="panel panel-'.$panel.'">
			<div class="panel-heading" style="text-align:right; vertical-align:middle;">
				<a href="'.$flClear.'" role="button" class="btn btn-default btn-xs"><span  class="caret" aria-hidden="true"></span></a>
				<b class="pull-left">'.$panelHead.'</b>
			</div>
		</div>';
	};




	### Hent alt indholdet (listevisning)

	#Til pagination og hvilke artikler der skal hentes
	$page = 0;
	if(isset($_GET['side'])){
		if(is_int($_GET['side']) == false){
			$page = $_GET['side'];
		}else{
			$page = 0;
		};
	};

	#query databasen - hent 25 artikeler per side
    $count = $page*25;
    $sql = "SELECT * FROM portfolio ORDER BY id DESC LIMIT ".$count.", 25";
    $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));

    #Panelet bliver ikke vist, hvis der ikke er artikler at vise.
    $num = mysqli_num_rows($query);
    if($num != 0){

    #Print artikler
    $br = 0;
	echo'<div class="panel panel-default">';
		echo'<div class="panel-heading">';
			echo'<b>Portfolio oversigt</b>';
		echo'</div>';
		echo'<div class="panel-body">';
			echo'<table class="table table-striped" style="width:100%;">';
			echo'<thead>';
				echo'<tr>';
					echo'<th style="text-align:left;"><small class="text-muted">Projekt</small></th>';
					echo'<th style="text-align:center;"><small class="text-muted">Counter</small></th>';
					echo'<th style="text-align:center;"><small class="text-muted">Medie</small></th>';
					echo'<th style="text-align:right;"><small class="text-muted">Oprettet</small></th>';
				echo'</tr>';
			echo'</thead>';
		    while($result = mysqli_fetch_array($query)){

		    	#Bestem antal billeder - medie query
		    	$msql = "SELECT id FROM media WHERE portfolio=1 AND dl=0 AND mid=".$result['id'];
		    	$mquery = mysqli_query($conn,$msql)or die(mysqli_error($conn));
		    	$num = mysqli_num_rows($mquery);
		    	#Ret skrivefejl, hvis der kun er 1 billede (billeder)
		    	if($num == 1){
		    		$conVal = "billede";
		    	}else{
		    		$conVal = "billeder";
		    	};

		    	#ret skrivefejl til counteren (visninger)
		    	if($result['counter'] == 1){
		    		$countVal = "visning";
		    	}else{
		    		$countVal = "visninger";
		    	};

		    	#download
		    	$msql2 = "SELECT id FROM media WHERE portfolio=1 AND dl=1 AND mid=".$result['id'];
		    	$mquery2 = mysqli_query($conn,$msql2)or die(mysqli_error($conn));
		    	$num2 = mysqli_num_rows($mquery2);
		    	$dl = NULL;
		    	if($num2 == true){
		    		$dl = " + download";
		    	};

		    	#Visuelt flottere links
		    	$qsPage = NULL;
		    	if($page != 0){
		    		$qsPage = "side=".$page."&";
		    	};

		    	#Hent kategori
		    	$catSql = "SELECT category FROM categories WHERE id='".$result['category']."'";
		    	$catQuery = mysqli_query($conn,$catSql) or die(mysqli_error($conn));
		    	$catResult = mysqli_fetch_array($catQuery);

		    	#Print artiklerne - html delen
		    	echo'<tr>';
		    	echo'<td>
		    			<a href="godmode.php?portfolio&'.$qsPage.'rediger='.$result['id'].'" title="Rediger projekt '.$result['id'].'"><b>'.$result['headline'].'</b></a>
		    			<br />
		    			<small class="text-muted">'.$catResult['category'].'</small>
		    		</td>';
		    	echo'<td style="vertical-align:bottom; text-align:center;">
		    			'.$result['counter'].' '.$countVal.'
		    		</td>';
		    	echo'<td style="vertical-align:bottom; text-align:center;">
		    			<small class="text-muted">'.$num.' '.$conVal.$dl.'</small>
		    		</td>';
		    	echo'<td style="vertical-align:bottom; text-align:right;">
		    			<small class="text-muted">'.$result['time'].'</small>
		    		</td>';
		    	echo'</tr>';


		    	$br++;
		    };
		    echo'</table>';
		echo'</div>';
	echo'</div>';

	};




    #Pagination
	$sql = "SELECT id FROM portfolio";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$num = mysqli_num_rows($query);

	$p = $page - 1;
	$n = $page + 1;
	$nc = $num-(($page+1)*25);

	#Visuelt flottere links
	$fl = NULL;
	if(isset($_GET['fl'])){
		$fl = "fl&";
	};

	#Knapperne - kun vist hvis der er artikler til det
	if($page != 0){
		echo'<a class="btn btn-primary" href="godmode.php?portfolio&'.$fl.'side='.$p.'" title="">Forrige</a>';
	};
	if($nc != 0 && ($num / $nc) > 1){
		echo'<a class="btn btn-primary pull-right" style="margin-bottom:2%;" href="godmode.php?portfolio&'.$fl.'side='.$n.'" title="">Næste</a>';
	};


	#Gentager lige lidt javascript et par gange (billedbeskrivelse, næste billede og preview mulighed)
	$br = 1;
	echo'<script>';
	while($br <= 5){

		#smallTxt (billedbeskrivelse)
		echo'function smallTxt'.$br.'(){';
			echo'document.getElementById("imgTxt'.$br.'").style.visibility = "visible";';
			echo'document.getElementById("imgTxt'.$br.'").style.display = "inline";';
		echo'};';

		if($br != 1){
			#imgBox (næste billede)
			echo'function imgBox'.$br.'(){';
				echo'document.getElementById("imgBox'.$br.'").style.visibility = "visible";';
				echo'document.getElementById("imgBox'.$br.'").style.display = "inline";';
			echo'};';

			#radio (preview mulighed)
			echo'function radio'.$br.'(){';
				echo'document.getElementById("radio'.$br.'").style.visibility = "visible";';
				echo'document.getElementById("radio'.$br.'").style.display = "inline";';
			echo'};';
		};
		$br++;
	};
	echo'</script>';


?>

<script>
//wysiwyg
$(document).ready(function() {
    $('#summernote').summernote({
        height:200,
        toolbar: [
            ['insert',['picture','link','video']],
            ['font style',['fontsize','color','bold','italic','underline','strikethrough']],
            ['para',['ol','ul','paragraph']],
            ['misc',['undo','codeview']]
        ]
    });
})


//Bekræft når man vil slette tilhørende medie
function delCon(id){
	var b = confirm("Er du sikker?");
	if (b == true) {
	    window.location = "godmode/php/delMedia.php?rel=portfolio&id=" + id;
	};
};

//Bekræft når man vil slette hele projektet
function delCont(id){
	var b = confirm("Er du sikker?");
	if (b == true) {
	    window.location = "godmode/php/delContent.php?rel=portfolio&id=" + id;
	};
};

</script>