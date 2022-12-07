<?php
	function checkUser()
	{
		if (!isset($_SESSION["murdoch_email"]))
		{
			header("Location:./pages/Login.php");
		}
	}
?>