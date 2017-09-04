<?php

	#Fjerner session og sender tilbage (session som gør, at man kun kan sende en besked af gangen)
	@session_start();
	if(isset($_SESSION['mailSendt'])){
		unset($_SESSION['mailSendt']);
	};
	header("location:../kontakt.php");


?>