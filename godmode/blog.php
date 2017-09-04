<?php
	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (blog.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
	};



	#Skifter panelet, alt an på status
	$panelHead = "Opret artikel";
	$panel = "default";
	if(isset($_GET['rediger'])){
		$panelHead = "Rediger artikel";
	};
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

	#Hvis opret artikel panel er åbnet
	if(!isset($_GET['fl'])){
		$headline = NULL;
		$txt = NULL;
		$edit = NULL;
		$subVal = "Opret";
		$required = "required";
		$newDevel = NULL;
		$newPort = NULL;
		if(isset($_GET['rediger'])){
			$id = mysqli_real_escape_string($conn,$_GET['rediger']);
			$sql = "SELECT * FROM blog WHERE id='".$id."'";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$result = mysqli_fetch_array($query);
			$txt = $result['txt'];
			$headline = $result['headline'];
			$edit = $result['id'];
			$subVal = "Opdater artikel";
			$sql = "SELECT id,file,txt FROM media WHERE blog=1 AND mid='".$id."'";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$num = mysqli_num_rows($query);
			if($num == true){
				$buttonVal = true;
			};
			$required = NULL;
		};

		#Hvis man lukker panelet
		$page = NULL;
		if(isset($_GET['side'])){
			$page = "&side=".$_GET['side'];
		};
		$close = "?fl&blog".$page;


	echo'<div class="panel panel-'.$panel.'">';
		echo'<div class="panel-heading">';
			echo'<b>'.$panelHead.'</b>';
			echo'<a href="'.$_SERVER['PHP_SELF'].$close.'" type="button" class="close" role="button" aria-label="Close"><span aria-hidden="true">&times;</span></a>';
		echo'</div>';

		echo'<div class="panel-body">';

		#Opret/rediger artikel - formula
		echo'
			<form method="post" style="margin-top:.6%;" action="godmode/php/createBlog.php" enctype="multipart/form-data">
				<label for="headline">Overskrift</label>
				<input type="text" id="headline" name="headline" style="margin-bottom:1%;" value="'.$headline.'" class="form-control" required>
				<input type="hidden" name="edit" value="'.$edit.'"/>';
		echo'
				<label for="keywords">Keywords (adskil med komma)</label>
				<input type="text" name="keywords" id="keywords" class="form-control" style="margin-bottom:1%;" '.$required.'/>';
				if($edit == true){
					echo'<label for="keywords">Tilhørende keywords</label>';
					echo'<div id="keywords" style="margin-bottom:1%; width:100%; padding:0 .5% 0 .5%;">';
						$keySql = "SELECT keyword,id FROM keywords WHERE blog=1 AND mid='".$edit."'";
						$keyQuery = mysqli_query($conn,$keySql)or die(mysqli_error($conn));
						$br = 0;
						while($keyResult = mysqli_fetch_array($keyQuery)){
							$comma = ", &nbsp;";
							if($br == 0){
								$comma = NULL;
								$br++;
							};
							echo $comma."<b>".$keyResult['keyword']."</b>(<a href='godmode/php/delContent.php?rel=keywords&id=".$keyResult['id']."' title='Slet keyword'>Slet</a>)";
						};
					echo'</div>';
				};
		echo'
				<label for="txt">Artikel</label>';

				//wysiwyg
				echo'<textarea name="txt" class="form-control" id="summernote" style="margin-bottom:1%;" required>'.$txt.'</textarea>';

					#Billede mulighed - hvis man opretter artikel, eller et tidligere billede ikke er sat
					if(!isset($buttonVal)){
						echo'
						<input type="file" name="file" onchange="smallTxt();" id="imgTmp" class="filestyle" data-buttonText="Indsæt billede" data-buttonName="btn-primary" data-buttonBefore="true">
						<input type="text" style="margin-top:1%; visibility:hidden; display:none;" id="imgTxt" name="fileTxt" placeholder="Billede beskrivelse" class="form-control">';
					#Billede mulighed - når man redigerer artikel og et tidligere billede er sat
					}else{

						$imgResult = mysqli_fetch_array($query);
						echo'<script> var id = "'.$result['id'].'"; </script>';
						echo'<div class="media" style="margin:3% 0 3% 0;">';
							echo'<div class="media-left" style="width:50%; text-align:center;">';
								echo'<img class="" style="width:75%; border:1px solid grey;" src="'.$imgResult['file'].'"/>';
							echo'</div>';
							echo'<div class="media-body" style="padding-right:6%">';
								echo'<label for="eImgLoc">Billedeplacering</label>';
								echo'<input type="text" id="eImgLoc" value="'.$imgResult['file'].'" class="form-control" style="margin-bottom:2%;" disabled>';
								echo'<label for="eImgTxt">Billedebeskrivelse</label>';
								echo'<input type="text" id="eImgTxt" name="fileTxt" value="'.$imgResult['txt'].'" placeholder="Billedebeskrivelse" style="margin:0 0 2%;" class="form-control"/>';
								echo'<a onclick="delImgCon();" class="btn btn-danger btn-block marginTop2" role="button">Slet billede</a>';
								echo'<div style="margin-top:5%;">';
									echo'<label for="eImgNew">Overskriv med nyt billede</label>';
									echo'<input type="file" id="eImgNew" name="file" class="filestyle" style="float:left;" data-buttonText="Nyt billede" data-buttonName="btn-primary" data-buttonBefore="true">';
								echo'</div>';
							echo'</div>';
						echo'</div>';
					};
					if($edit == true){
						echo'<a onclick="delCon('.$result['id'].')" style="margin-top:2%;" class="btn btn-danger btn-block">Slet artikel</a>';
					};
				echo'
				<input type="submit" name="okCreate" class="btn btn-primary btn-block" style="margin-top:1%;" value="'.$subVal.'">
			</form>
		</div>
	</div>';

	#Lukket panel til oprettelse af artikel
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




	###################
	#	Artikler
	###################

	#Panelet bliver ikke vist, hvis der ikke er artikler at vise.
	$sql = "SELECT * FROM blog ORDER BY id DESC";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
    $num = mysqli_num_rows($query);
    if($num != 0){

	#Til pagination og hvilke artikler der skal hentes
	$page = 0;
	if(isset($_GET['side'])){
		if(is_int($_GET['side']) == false){
			$page = $_GET['side'];
		}else{
			$page = 0;
		};
	};

    #Print artikler
    $br = 0;
	echo'<div class="panel panel-default">';
		echo'<div class="panel-heading">';
			echo'<b>Artikel oversigt</b>';
		echo'</div>';
		echo'<div class="panel-body">';
			echo'<table class="table table-striped" style="width:100%;">';
			echo'<thead>';
				echo'<tr>';
					echo'<th style="text-align:left;"><small class="text-muted">Overskrift</small></th>';
					echo'<th style="text-align:center;"><small class="text-muted">Antal keywords</small></th>';
					echo'<th style="text-align:center;"><small class="text-muted">Medie</small></th>';
					echo'<th style="text-align:right;"><small class="text-muted">Oprettet</small></th>';
				echo'</tr>';
			echo'</thead>';
		    while($result = mysqli_fetch_array($query)){

		    	#Visuelt flottere links
		    	$qsPage = NULL;
		    	if($page != 0){
		    		$qsPage = "side=".$page."&";
		    	};

		    	$keySql = "SELECT id FROM keywords WHERE blog=1 AND mid=".$result['id'];
		    	$keyQuery = mysqli_query($conn,$keySql)or die(mysqli_error($conn));
		    	$keyNum = mysqli_num_rows($keyQuery);

		    	$imgSql = "SELECT id FROM media WHERE blog=1 AND mid=".$result['id'];
		    	$imgQuery = mysqli_query($conn,$imgSql)or die(mysqli_error($conn));
		    	$img = NULL;
		    	if(mysqli_num_rows($imgQuery) != 0){
		    		$img = "Ja";
		    	};

		    	#Print artiklerne - html delen
		    	echo'<tr>';
		    	echo'<td>
		    			<a href="godmode.php?blog&'.$qsPage.'rediger='.$result['id'].'" title="Rediger artikel '.$result['id'].'"><b>'.$result['headline'].'</b></a>
		    		</td>';
		    	echo'<td style="vertical-align:bottom; text-align:center;">
		    			'.$keyNum.'
		    		</td>';
		    	echo'<td style="vertical-align:bottom; text-align:center;">
		    			'.$img.'
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
	$sql = "SELECT id FROM blog";
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
		echo'<a class="btn btn-primary" href="godmode.php?blog&'.$fl.'side='.$p.'" title="">Forrige</a>';
	};
	if($nc != 0 && ($num / $nc) > 1){
		echo'<a class="btn btn-primary pull-right" href="godmode.php?blog&'.$fl.'side='.$n.'" title="">Næste</a>';
	};

?>





<script>

	//Bekræft når man vil slette et billede
	function delImgCon(){
		var r = confirm("Er du sikker?");
		if (r == true) {
		    window.location = "godmode/php/delMedia.php?id=" + id;
		};
	};

	//Bekræft når man vil slette en artikel
	function delCon(id){
		var b = confirm("Er du sikker?");
		if (b == true) {
		    window.location = "godmode/php/delContent.php?rel=blog&id=" + id;
		};
	};


	//Viser billede tekst form, når billedet er sat
	function smallTxt(){
		document.getElementById("imgTxt").style.visibility = "visible";
		document.getElementById("imgTxt").style.display = "inline";
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