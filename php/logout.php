<?php

	#Fjerner alle sessions
	@session_start();
	session_destroy();
	header("location:../");
?>