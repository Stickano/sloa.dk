<?php

	@session_start();
	include("functions.php");
	include("connection.php");

	#Til portfolio filtrering
	if(isset($_POST['okFilter']) || isset($_POST['count'])){

		$link = "";

		#Filter
		if(isset($_POST['web'])){
			$link .= "w";
		};
		if(isset($_POST['design'])){
			$link .= "d";
		};
		if(isset($_POST['everythingelse'])){
			$link .= "a";
		};

		#Sortering
		if($_POST['order'] == "latest"){
			$link .= "s";
		}elseif($_POST['order'] == "category"){
			$link .= "k";
		#}else{
			#$link .= "t";
		};

		#antal per side
		$link .= $_POST['count'];


		header("location:../portfolio.php?filter=".$link);
	};
?>