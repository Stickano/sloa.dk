<?php
	@session_start();
	include("../../php/connection.php");
	include("../../php/functions.php");

	#Sikkerhed
	if(!isset($_SESSION['sloaLogged'])){
		$client = mysqli_real_escape_string($conn,clientIP());
		$time = mysqli_real_escape_string($conn,timeMe());
		$sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (php/createBlog.php)','".$time."',1,'sikkerhed')";
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

		#Opret/opdater blog artiklen
		$blogDanger = 0;
		if($edit != true){
			$sql = "INSERT INTO blog (headline,time,txt) VALUES ('".$headline."','".$time."','".$txt."')";
		}else{
			$sql = "UPDATE blog SET headline='".$headline."', txt='".$txt."' WHERE id='".$edit."'";
		};
		if(mysqli_query($conn,$sql)){
			$sql = "SELECT id FROM blog ORDER BY id DESC LIMIT 1";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$result = mysqli_fetch_array($query);
			if($edit != true){
				$blogEvent = "Artikel #".$result['id']." oprettet";
				$_SESSION['createSuccess'] = "Artikel oprettet";
			}else{
				$blogEvent ="Artikel #".$edit." blev redigeret";
				$_SESSION['createSuccess'] = "Artikel opdateret";
			};
		}else{
			$_SESSION['createError'] = "Ukendt fejl!";
			$blogDanger = 1;
			if($edit != true){
				$blogEvent = "Fejl ved oprettelse af artikel ".mysqli_error($conn);
			}else{
				$blogEvent = "Fejl ved redigering af artikel #".$edit." ".mysqli_error($conn);
			};
		};

		#Opret log
		$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$ip."','".$blogEvent."','".$time."','".$uid."','".$blogDanger."','blog')";
		mysqli_query($conn,$sql)or die(mysqli_error($conn));


		#Opret keywords
		if(!empty($_POST['keywords']) && $blogDanger != 1){
			$key = explode(",", $_POST['keywords']);
			$keyCount = count($key);
			$br = 0;
			while($br < $keyCount){
				$id = $result['id'];
				if($edit == true){
					$id = $edit;
				};
				$keyword = $key[$br];
				$keyword = mysqli_real_escape_string($conn,$keyword);
				$br++;
				$sql = "INSERT INTO keywords (keyword,blog,mid) VALUES ('".$keyword."',1,".$id.")";
				if(!mysqli_query($conn,$sql)){
					$blogEvent = "Fejl ved oprettelse af keywords ".mysqli_error($conn);
					$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$ip."','".$blogEvent."','".$time."','".$uid.",1,'blog')";
					mysqli_query($conn,$sql)or die(mysqli_error($conn));
				};
			};
		};


		#######	Mangler mere sikkerhed

		#Billede upload
		if($_FILES['file']['size'] > 0 && $blogDanger != 1){
			$imgTxt = mysqli_real_escape_string($conn,$_POST['fileTxt']);

			#Definer blogID'et
			if($edit != true){
				$sql = "SELECT id FROM blog ORDER BY id DESC LIMIT 1";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$result = mysqli_fetch_array($query);
				$blogID = $result['id'];
			}else{
				$blogID = $edit;
			};

			#Opret mappe, hvis nødvendigt
			if(!is_dir("../../media/blog/".$blogID)){
				mkdir('../../media/blog/'.$blogID);
			};

			#definere lidt forskelligt
			$imgDanger = 0;
			$filetypes = array("jpg","jpeg","pjpeg","gif","png");
			$dir = "../../media/blog/".$blogID."/";
			$file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			$mtime = microtime();
			$locktime = substr(md5($mtime),0,6);
			$aa = $dir.$locktime.".".$file_ext;
			$cleanPath = substr($aa,6);

			#Skal være billede og må ikke overskride ....190MB - hah! Server bestemt ;)
			if(!in_array($file_ext,$filetypes)){
				$_SESSION['createError'] = "Fil type var ikke accepteret (JPG, GIF & PNG)";
				$imgEvent = "Ikke accepteret filtype";
				$imgDanger = 1;
			};

			if($_FILES['file']['size'] >= 199229000){
				$_SESSION['createError'] = "Overskrider 190MB ('ish)";
				$imgEvent = "Større end 190MB";
				$imgDanger = 1;
			};

			#Hvis man opdaterer en artikel, tjek og slet aktuelle billede
			if($edit == true){
				$sql = "SELECT id,file FROM media WHERE mid='".$edit."'";
				$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
				$num = mysqli_num_rows($query);
				if($num == true){
					$result = mysqli_fetch_array($query);
					if(unlink("../../".$result['file'])){
						$sql = "DELETE FROM media WHERE id=".$result['id'];
						mysqli_query($conn,$sql)or die(mysqli_error($conn));
						$event = "Filen ".$result['file']." blev succesfuldt slettet";
						$sql = "INSERT INTO events (ip,event,time,uid,rel) VALUES ('".$ip."','".$event."','".$time."','".$uid."','blog')";
						mysqli_query($conn,$sql)or die(mysqli_error($conn));
					}else{
						$_SESSION['createError'] = "Det aktuelle billede kunne ikke slettes";
						$imgEvent = "Filen ".$result['file']." blev ikke slettet";
						$imgDanger = 1;
					};
				};
			};



			#Smid tilbage hvis fejl
			if($imgDanger == true){
				#slet mappen, hvis formodet tom
				if($edit != true){
					$rmDir = $blogID;
					rrmdir("../../media/blog/".$rmDir);
				};
				$sql = "INSERT INTO events (ip,event,time,uid,rel,danger) VALUES ('".$ip."','".$imgEvent."','".$time."','".$uid."','blog',".$imgDanger.")";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
				header("location:../../godmode.php?blog&rediger=".$blogID);
				exit;
			};

			#Forsøg at overfør fil
			if(move_uploaded_file($_FILES['file']['tmp_name'],$aa)){
				$imgEvent = "Fil overført til ".$cleanPath;
				$sql = "INSERT INTO media (file,mid,txt,blog) VALUES ('".$cleanPath."',".$blogID.",'".$imgTxt."',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			}else{
				$_SESSION['createError'] = "Ukendt fejl opstået ved overførsel #".$_FILES['file']['error'];
				$imgEvent = "Fejl ved overførsel af ".$cleanPath." #".$_FILES['file']['error'];
				$imgDanger = 1;
			};

			#Opret log
			$sql = "INSERT INTO events (ip,event,time,uid,danger,rel) VALUES ('".$ip."','".$imgEvent."','".$time."','".$uid."',".$imgDanger.",'blog')";
			mysqli_query($conn,$sql)or die(mysqli_error($conn));

		};

		header("location:../../godmode.php?fl&blog");
	};
?>