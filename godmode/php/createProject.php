<?php
	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/createProject.php)','".$time."',1,'sikkerhed')";
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



	if(isset($_POST['okCreate'])){


		#Klargør input
		$headline = mysqli_real_escape_string($conn,$_POST['headline']);
		$txt = mysqli_real_escape_string($conn,$_POST['txt']);
		$time = mysqli_real_escape_string($conn,timeMe());
		$ip = mysqli_real_escape_string($conn,clientIP());
		$uid = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);
		$edit = mysqli_real_escape_string($conn,$_POST['edit']);
		$category = mysqli_real_escape_string($conn,$_POST['category']);
		$reference = mysqli_real_escape_string($conn,$_POST['reference']);

		#Opret/opdater blog artiklen
		$portDanger = 0;
		if($edit != true){
			$sql = "INSERT INTO portfolio (headline,time,txt,reference,user,category) VALUES ('".$headline."','".$time."','".$txt."','".$reference."','".$uid."','".$category."')";
		}else{
			$sql = "UPDATE portfolio SET headline='".$headline."', txt='".$txt."', reference='".$reference."', category='".$category."' WHERE id='".$edit."'";
		};
		if(mysqli_query($conn,$sql)){
			if($edit != true){
				$sql = "SELECT id FROM portfolio ORDER BY id DESC LIMIT 1";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$result = mysqli_fetch_array($query);
				$portEvent = "Projekt #".$result['id']." oprettet";
				$_SESSION['createSuccess'] = "Projekt oprettet";
			}else{
				$portEvent ="Projekt #".$edit." blev redigeret";
				$_SESSION['createSuccess'] = "Projekt opdateret";
			};
		}else{
			$_SESSION['createError'] = "Ukendt fejl!";
			$portDanger = 1;
			if($edit != true){
				$portEvent = "Fejl ved oprettelse af projekt";
			}else{
				$portEvent = "Fejl ved redigering af projekt #".$edit;
			};
		};


		#Opret log
		$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$ip."','".$portEvent."','".$time."','".$uid."','".$portDanger."','portfolio')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));




		#Billede upload

		#Definer hvor mange billeder der skal uploades
		$files = 0;
		if(isset($_FILES['file1'])){
			if($_FILES['file1']['size'] > 0){
				$files = 1;
			};
			if($_FILES['file2']['size'] > 0){
				$files = 2;
			};
			if($_FILES['file3']['size'] > 0){
				$files = 3;
			};
			if($_FILES['file4']['size'] > 0){
				$files = 4;
			};
			if($_FILES['file5']['size'] > 0){
				$files = 5;
			};
		};

		#Fortsæt med billede upload, hvis der ikke er registreret fejl
		if($files > 0 && $portDanger != 1){

			#Definer portfolioID'et
			if($edit != true){
				$sql = "SELECT id FROM portfolio ORDER BY id DESC LIMIT 1";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$result = mysqli_fetch_array($query);
				$portID = $result['id'];
			}else{
				$portID = $edit;
			};



			#Opret mappe, hvis nødvendigt
			if(!is_dir("../../media/portfolio/".$portID)){
				mkdir('../../media/portfolio/'.$portID);
			};

			#definere lidt forskelligt
			$imgDanger = 0;
			$filetypes = array("jpg","jpeg","pjpeg","gif","png");
			$dir = "../../media/portfolio/".$portID."/";
			$pre = mysqli_real_escape_string($conn,$_POST['primary']);


			#Upload billeder x antal gange nødvendigt
			$br = 1;
			while($br <= $files){

				#Definer lidt typisk
				$file_ext = strtolower(pathinfo($_FILES['file'.$br]['name'], PATHINFO_EXTENSION));
				$mtime = microtime();
				$locktime = substr(md5($mtime),0,6);
				$aa = $dir.$locktime.".".$file_ext;
				$cleanPath = substr($aa,6);
				$imgTxt = mysqli_real_escape_string($conn,$_POST['fileTxt'.$br]);

				#sæt preview
				$preview = 0;
				if($pre == $br){
					$preview = 1;
				};

				#Skal være billede og må ikke overskride ....190MB - hah! Server bestemt ;)
				if(!in_array($file_ext,$filetypes)){
					$_SESSION['createError'] = "(".$br."/".$files.") Fil type var ikke accepteret (JPG, GIF & PNG)";
					$imgEvent = "(".$br."/".$files.") Ikke accepteret filtype";
					$imgDanger = 1;
				};

				if($_FILES['file'.$br]['size'] >= 199229000){
					$_SESSION['createError'] = "(".$br."/".$files.") Overskrider 190MB ('ish)";
					$imgEvent = "(".$br."/".$files.") Større end 190MB";
					$imgDanger = 1;
				};

				#Smid tilbage hvis fejl
				if($imgDanger == true){
					$sql = "INSERT INTO events (ip,event,time,uid,rel,danger) VALUES ('".$ip."','".$imgEvent."','".$time."','".$uid."','portfolio',".$imgDanger.")";
					mysqli_query($conn,$sql)or die(mysqli_error($conn));
					header("location:../../godmode.php?portfolio&rediger=".$portID);
					exit;
				};

				#Forsøg at overfør fil
				if(move_uploaded_file($_FILES['file'.$br]['tmp_name'],$aa)){
					$imgEvent = "(".$br."/".$files.") Fil overført til ".$cleanPath;
					$sql = "INSERT INTO media (file,mid,txt,portfolio,preview) VALUES ('".$cleanPath."',".$portID.",'".$imgTxt."',1,'".$preview."')";
					mysqli_query($conn,$sql)or die(mysqli_error($conn));
				}else{
					$_SESSION['createError'] = "Ukendt fejl opstået ved overførsel (".$br."/".$files.") #".$_FILES['file'.$br]['error'];
					$imgEvent = "(".$br."/".$files.") Fejl ved overførsel af ".$cleanPath." #".$_FILES['file'.$br]['error'];
					$imgDanger = 1;
				};

				#Opret log
				$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$ip."','".$imgEvent."','".$time."','".$uid."',".$imgDanger.",'portfolio')";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));

				$br++;
			};

		};

		#Hvis hentbar fil er inkl.
		$download = NULL;
		if($_FILES['download']['size'] > 0){

			#definere lidt forskelligt
			$dlDanger = 0;
			$filetypes = array("zip");
			$dir = "../../media/portfolio/".$portID."/";
			$file_ext = strtolower(pathinfo($_FILES['download']['name'], PATHINFO_EXTENSION));
			$mtime = microtime();
			$locktime = substr(md5($mtime),0,6);
			$aa = $dir."sloa.dk - ".$locktime.".".$file_ext;
			$cleanPath = substr($aa,6);

			#Tjek og slet om allerede eksisterende hentbar fil, hvis man redigere
			if($edit == true){
				$portID = $edit;
				$sql = "SELECT id,file,mid FROM media WHERE dl=1 AND mid='".$edit."'";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				if(mysqli_num_rows($query) == true){
					#slet fil
					$result = mysqli_fetch_array($query);
					if(unlink("../../".$result['file'])){
						#slet fra databasen
						$sql = "DELETE FROM media WHERE id='".$result['id']."'";
						mysqli_query($conn,$sql)or die(mysqli_error($conn));
						#opret log
						$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$ip."','(dl) ".$result['file']." blev slettet fra projekt#".$result['mid']."','".$time."','".$uid."',".$imgDanger.",'portfolio')";
						mysqli_query($conn,$sql)or die(mysqli_error($conn));
					}else{
						$dlDanger = 1;
						$_SESSION['createError'] = "Den allerede eksisterende download fil kunne ikke slettes";
						$dlEvent = "(dl) Eksisterende download kunne ikke slettes";
					};
				};
			};

			#Skal være en .zip og må ikke overskride ....190MB - hah! Server bestemt ;)
			if(!in_array($file_ext,$filetypes)){
				$_SESSION['createError'] = "Download filen blev ikke accepteret (.zip)";
				$dlEvent = "(dl) Ikke accepteret filtype";
				$dlDanger = 1;
			};

			if($_FILES['download']['size'] >= 199229000){
				$_SESSION['createError'] = "Download filen overskrider 190MB ('ish)";
				$dlEvent = "(dl) Større end 190MB";
				$dlDanger = 1;
			};

			#Smid tilbage hvis fejl
			if($dlDanger == true){
				$sql = "INSERT INTO events (ip,event,time,uid,rel,danger) VALUES ('".$ip."','".$dlEvent."','".$time."','".$uid."','portfolio',".$dlDanger.")";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				header("location:../../godmode.php?portfolio&rediger=".$portID);
				exit;
			};

			#Forsøg at overfør fil
			if($dlDanger == 0 && move_uploaded_file($_FILES['download']['tmp_name'],$aa)){
				$dlEvent = "(dl) Fil overført til ".$cleanPath;
				#opret i medie db
				$sql = "INSERT INTO media (file,mid,portfolio,dl) VALUES ('".$cleanPath."',".$portID.",1,1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			}else{
				$_SESSION['createError'] = "Ukendt fejl opstået ved overførsel (dl) #".$_FILES['download']['error'];
				$dlEvent = "(dl) Fejl ved overførsel af ".$cleanPath." #".$_FILES['download']['error'];
				$dlDanger = 1;
			};

			#Opret log
			$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$ip."','".$dlEvent."','".$time."','".$uid."',".$dlDanger.",'portfolio')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));
		};

		header("location:../../godmode.php?fl&portfolio");
	};

?>