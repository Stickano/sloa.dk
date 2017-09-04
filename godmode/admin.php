<?php
	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (admin.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
	};


	#Teaser besked for automatisk oprettelse af profiler
	$intro = '<h4>Lad panelet oprette en administrator profil for dig!</h4>
			<p>
				En mail bliver sendt til den indtastede adresse med et midlertidigt kodeord.
				<br/><br />
				Ved første login, som sker gennem en krypteret sessions side, vil brugeren bliver spurgt efter ønskede oplysninger til sin profil.
				<br /><br />
				Bliver e-mail linket ikke anvendt i løbet af 48 timer, ophører muligheden for den konto.
				<br /><br />
				<span class="text-muted"><small><b>Det er planen ihvertfald^</b></small></span>
			</p>';

	#Skifter navn på submit knappen, en funktion derefter bliver kørt i næste dokument *
	$okName = "okMail";
	$okVal = "Send";

	#Besked hvis man hellere vil oprette selv
	if(isset($_GET['man'])){
		$intro = '<h4>..Eller opret profilen selv!</h4>
				<p>
					Der skal selvfølgelig også være plads til manuelt arbejde.
					<br /><br />
					Der vil ikke blive gemt en krypteret session for manuelt oprettede brugere.
					<br /><br />
					De bliver oprettet direkte i databasen, men brugeren bliver stadig gjort opmærksom over en mail.
					<br /><br />
					Jeg har taget mig friheden til at generere et tilfældigt kodeord til dig, men skift det endeligt hvis du lyster.
				</p>';

		#Submit knap *
		$okName = "okMan";
		$okVal = "Opret";
	};

	#Random password fyldt ud allerede
	$pwRand = substr(md5(sha1(rand(0,200))),0,6);

	#Skifter panelets top, hvis en profil er oprettet eller en fejl er sket
	$panel = "default";
	$panelTxt = "Tilføj administrator profil";
	if(isset($_SESSION['adminSuccess'])){
		$panel = "success";
		$panelTxt = "Profil oprettet";
		unset($_SESSION['adminSuccess']);
	};

	if(isset($_SESSION['adminError'])){
		$panel = "danger";
		$panelTxt = $_SESSION['adminError'];
		unset($_SESSION['adminError']);
	};

	if(isset($_SESSION['guestWarning'])){
		$panelTxt = "Gæstprofil registreret";
		$panel = "warning";
		unset($_SESSION['guestWarning']);
	};

	#QueryString
	$qs = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];

	#Tilbage til automatisk oprettelse
    $manSearch = strpos($_SERVER['QUERY_STRING'],"&man");
    $manClear = substr_replace($_SERVER['QUERY_STRING'],'',$manSearch,4);
    $manClear = $_SERVER['PHP_SELF']."?".$manClear;

	#Åben opret panel
    $flSearch = strpos($_SERVER['QUERY_STRING'],"fl&");
    $flClear = substr_replace($_SERVER['QUERY_STRING'],'',$flSearch,3);
    $flClear = $_SERVER['PHP_SELF']."?".$flClear;

    #luk velkomst panel
    $fl = $_SERVER['PHP_SELF']."?fl&".$_SERVER['QUERY_STRING'];

	#Tilføj administrator panel
	if(!isset($_GET['fl'])){
		echo'<div class="panel panel-'.$panel.'">';
			echo'<div class="panel-heading"><b>'.$panelTxt.'</b>
				<a href="'.$fl.'" title="Luk panel" role="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></a></div>';
			echo'<div class="panel-body">';
				echo'<div class="pull-right" style="width:50%; margin-top:.5%; padding-right:5%;">';
					echo $intro;
				echo'</div>';

				echo'<form method="post" style="width:45%;" action="godmode/php/addProfile.php">';
					echo'<label for="mail">E-mail (login)</label>';
					echo'<input type="email" id="mail" name="mail" class="form-control input-sm" required>';

					#De resterende felter, når man indtaster manuelt
					if(isset($_GET['man'])){
						echo'<label for="uname" style="margin-top:1%;">Brugernavn</label>';
						echo'<input type="text" name="uname" class="form-control input-sm" id="uname" required/>';
						echo'<label for="upass" style="margin-top:1%;">Kodeord</label>';
						echo'<input type="text" name="upass" value="'.$pwRand.'" class="form-control input-sm" id="upass" required/>';
						echo'<label for="web" style="margin-top:1%;">Hjemmeside</label>';
						echo'<input type="text" name="web" class="form-control input-sm" id="web" />';
					};

					echo'<input type="submit" name="'.$okName.'" value="'.$okVal.'" style="margin-top:.8%;" class="btn btn-primary btn-sm">';

					#Knap til manuel oprettelse
					if(!isset($_GET['man'])){
						echo' eller <a href="'.$qs.'&man" title="Indtast oplysningerne manuelt"><b><small>opret profilen manuelt</small></b></a>';
					}else{
						echo' eller <a href="'.$manClear.'" title="Lad panelet oprette en profil"><b><small>opret profilen automatisk</small></b></a>';
					};
				echo'</form>';
			echo'</div>';
		echo'</div>';

	#Hvis panelet er lukket
	}else{
        #Panelet
	    echo'
		<div class="panel panel-default">
			<div class="panel-heading" style="text-align:right; vertical-align:middle;">
				<a href="'.$flClear.'" role="button" class="btn btn-default btn-xs"><span  class="caret" aria-hidden="true"></span></a>
				<b class="pull-left">Tilføj administrator profil</b>
			</div>
		</div>';
	};



	#Administrator profiler
	echo'<div class="panel-default" style="background-color:white;">';
		echo'<div class="panel-heading"><b>Administrator profiler</b></div>';
		echo'<div class="panel-body">';

			#Sørger for at &profil= er en tal værdi
	      $profile = 0;
	      if(isset($_GET['profil'])){
	        if(is_int($_GET['profil']) == false){
	            $profile = $_GET['profil'];
	        }else{
	            $profile = 0;
	        };
	      };

	      #Flottere links
	      $man = NULL;
	      if(isset($_GET['man'])){
	      	$man = "man&";
	      };


	      	#Hvis man ikke har en profil åben, hvis denne besked
	      	if($profile == 0){
				echo'<p><small><b style="text-muted">Vælg en profil for yderlige oplysninger</b></small></p>';


			#Hvis man har en profil åben
			}else{


				#Npr man lukker en profil
				$idLen = strlen($_GET['profil']);
				$idLen = 8+$idLen;
		        $flSearch = strpos($_SERVER['QUERY_STRING'],"&profil=".$_GET['profil']);
		        $flClear = substr_replace($_SERVER['QUERY_STRING'],'',$flSearch,$idLen);
		        $flClear = $_SERVER['PHP_SELF']."?".$flClear;


		        #Hent profilen
				$sql = "SELECT * FROM users WHERE id='".$profile."'";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				if(mysqli_num_rows($query) == true){

					$result = mysqli_fetch_array($query);

					#Udskriv data - nice and neat
					echo'<form class="form-horizontal" style="margin:1% 0 1%;">';
							echo'<div class="col-sm-2"></div>';
							echo'<div class="col-sm-9">';
								echo'<a href="" title="Slet profil" style="margin-bottom:.5%;" class="btn btn-danger btn-xs" role="button">Slet</a>';
								echo'<a href="'.$flClear.'" title="Luk profil" role="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></a>';
							echo'</div>';
							echo'<label class="control-label col-sm-2" for="mail">E-mail:</label>';
							echo'<div class="col-sm-9">';
								echo'<input type="text" name="mail" id="mail" class="form-control" value="'.ucfirst($result['mail']).'" disabled>';
							echo'</div>';
							echo'<label style="margin-top:.5%;" class="control-label col-sm-2" for="uname">Brugernavn:</label>';
							echo'<div style="margin-top:.5%;" class="col-sm-9">';
								echo'<input type="text" name="uname" id="uname" class="form-control" value="'.ucfirst($result['uname']).'" disabled>';
							echo'</div>';
						#Hvis en hjemmeside er oprettet, vis også den
						if(!empty($result['web'])){
							echo'<label style="margin-top:.5%;" class="control-label col-sm-2" for="web">Hjemmeside:</label>';
							echo'<div style="margin-top:.5%;" class="col-sm-9">';
								echo'<p class="form-control-static"><a href="'.$result['web'].'" title="Tager dig til '.$result['web'].'" target="_blank">'.$result['web'].'</a></p>';
							echo'</div>';
						};
					echo'</form>';
				};
			};
		echo'</div>';


		#Tabel til administrator profiler
		echo'<table class="table table-striped">';
			echo'<thead class="small text-muted" style="font-weight:bold;">';
				echo'<tr>';
					echo'<td><b>#</b></td>';
					echo'<td><b>Profil</b></td>';
					echo'<td class="text-right"><b>Oprettet</b></td>';
				echo'</tr>';
			echo'</thead>';
			echo'<tbody>';

		#Hent administratorer
		$sql = "SELECT * FROM users ORDER BY id ASC";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));

		#visuelt flottere links
		$fl = NULL;
		if(isset($_GET['fl'])){
			$fl = "fl&";
		};

		while($result = mysqli_fetch_array($query)){
			echo'<tr>';
				echo'<td>'.$result['id'].'</td>';
				echo'<td>';
					echo'<a href="'.$_SERVER['PHP_SELF']."?".$fl."admin&".$man.'profil='.$result['id'].'" title="Se Profil"><b><small>'.ucfirst($result['mail']).' </b>a.k.a.<b> '.ucfirst($result['uname']).'</small></b></a>';
				echo'</td>';
				echo'<td class="text-right">'.$result['created'].'</td>';
			echo'</tr>';
		};

			echo'</tbody>';
		echo'</table>';
	echo'</div>';

?>