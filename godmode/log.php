<?php
	#Sikkerhed
	@session_start();
	if(!isset($_SESSION['sloaLogged'])){
		include("../php/connection.php");
		include("../php/functions.php");
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (log.php)','".$time."',1,'sikkerhed')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));
		session_destroy();
		header("location:../");
	};


	#Bekræft der ikke mangler en log (sikkerhedsled)
	$sql = "SELECT id FROM events ORDER BY id DESC";
	$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	$num = mysqli_num_rows($query);

	$br = 0;
	$tampered = NULL;
	while($result = mysqli_fetch_array($query)){
		if(($result['id'] + $br) != $num){
			$tampered = TRUE;
		};
		$br++;
	};

	if(isset($tampered)){
		echo'<div class="alert alert-danger" style="margin-bottom:1%;" role="alert"><b>Advarsel!</b> Loggen stemmer ikke overens!</div>';
	};



	#Bekræft &side= er en tal værdi
	$page = 0;
	  if(isset($_GET['side'])){
	    if(is_int($_GET['side']) == false){
	        $page = $_GET['side'];
	    };
	  };

	  	#Til pagination - 100 events p. side
	    $count = $page*100;
	    $sql = "SELECT * FROM events ORDER BY id DESC LIMIT ".$count.", 100";
		$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));


		#Events i tabel
		echo'<table class="table table-condensed">';
			echo'<thead class="small text-muted">';
				echo'<tr class="active">';
					echo'<td style="margin:0 1% 0 1%; width:1px;">';
						echo'<b>#</b>';
					echo'</td>';
					if($sloaLogged != 2){
						echo'<td style="margin:0 1% 0 1%; width:1px;">';
							echo'<b>IP</b>';
						echo'</td>';
					};
					echo'<td style="margin:0 1% 0 1%; width:1px;">';
						echo'<b>UID</b>';
					echo'</td>';
					echo'<td style="margin:0 1% 0 1%; width:1px;">';
						echo"<b>Relateret</b>";
					echo'</td>';
					echo'<td style="margin:0 0 0 1%;">';
						echo'<b>Handling</b>';
					echo'</td>';
					echo'<td class="text-right">';
						echo'<b>Tidspunkt</b>';
					echo'</td>';
				echo'</tr>';
			echo'</thead>';

			#Hent events
			while($result = mysqli_fetch_array($query)){

				#Skift baggrundsfarve alt an' på omstændigheden, og hent bruger oplysningerne ud fra #uid
				$class = "success";
				$uid = "#";
				if($result['uid'] == TRUE){
					$uSql = "SELECT uname FROM users WHERE id=".$result['uid'];
					$uQuery = mysqli_query($conn,$uSql)or die(mysqli_error($conn));
					$uResult = mysqli_fetch_array($uQuery);
					$uid = "<abbr title='".$uResult['uname']."'>#".$result['uid']."</abbr>";
				};
				if($result['danger'] == 1){
					$class = "danger";
				};

				#Værdier
				echo'<tr class="'.$class.'">';
					echo'<td>';
						echo $result['id'];
					echo'</td>';
					if($sloaLogged != 2){
						echo'<td style="margin:0 1% 0 1%;">';
							echo $result['ip'];
						echo'</td>';
					};
					echo'<td style="text-align:center; margin:0 1% 0 1%;">';
						echo $uid;
					echo'</td>';
					echo'<td style="text-align:center; margin:0 1% 0 1%;">';
						echo $result['rel'];
					echo'</td>';
					echo'<td style="margin:0 1% 0 1%;">';

						#Hvis det er en farlig begivenhed, som ikke er set før, smid et lille label
						if($result['seen'] == 0 && $result['danger'] == 1){
							echo'<span class="label label-danger">Ny</span> ';
						};

						echo $result['event'];
					echo'</td>';
					echo'<td class="text-right">';
						echo $result['time'];
					echo'</td>';
				echo'</tr>';
			};
		echo'</table>';

	  #Pagination
	  $sql = "SELECT * FROM events";
	  $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
	  $num = mysqli_num_rows($query);

	  $p = $page - 1;
	  $n = $page + 1;
	  $nc = $num-(($page+1)*100);

	  if($page != 0){
	    echo'<a class="btn btn-primary pull-left" style="margin-bottom:2%;" href="?log&side='.$p.'" title="">Forrige</a>';
	  };
	  if($nc != 0 && ($num / $nc) > 1){
	    echo'<a class="btn btn-primary pull-right" style="margin-bottom:2%;"  href="?log&side='.$n.'" title="">Næste</a>';
	  };


#Nulstil "tælleren" for nye events
 if(isset($_GET['log'])){
    $sql = "SELECT seen FROM events WHERE seen=0";
    $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
    $num = mysqli_num_rows($query);
    if($num == true && $sloaLogged != 2){
      $sql = "UPDATE events SET seen=1";
      mysqli_query($conn,$sql)or die(mysqli_error($conn));
    };
  };
?>