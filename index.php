<?php
	session_start();
	
	if (!isset($_SESSION["Murdoch_Email"]))
	{
		header("Location:./pages/Login.php");
	}
	else
	{
		header("Location:./pages/home.php");
	}
?>