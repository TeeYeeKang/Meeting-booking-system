<?php
    session_start();
	if (!isset($_SESSION["Murdoch_ID"]))
	{
		header("Location:../pages/Login.php");
	}
    $conn = mysqli_connect('localhost', 'root', '', 'meetme') or die ('Unable to connect');
	$type = $_SESSION['Murdoch_ID']['type'];
	$murdID = $_SESSION['Murdoch_ID']['murdoch_id'];
	$murdEmail = $_SESSION['Murdoch_ID']['murdoch_email'];
	
	if(isset($_POST['deleteItem']))
	{
		$delete = $_POST['deleteItem'];
		$sql = "DELETE FROM bookings WHERE book_id = '$delete'";
		
		if($conn->query($sql))
		{
			echo "<meta http-equiv='refresh' content='0'>";
		}
	}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeetMe - Home</title>
    <link rel="stylesheet" href="../navstyle.css?v=<?php echo time(); ?>">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="/css/main.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>

<body>
    <nav>
        <a class="logo" href="../index.php">MEET ME</a>
        <ul>
          <li><a class="active" href="home.php">Home</a></li>
          <li><a href="bookings.php">Bookings</a></li>
		  <?php if($type == 'admin'){ ?>
		  <li><a href="upload.php">Upload</a></li>
		  <?php } ?>
          <li><a href="Logout.php">Log Out</a></li>
        </ul>
    </nav>
  
    <div class="h-container">
        <h1 class="up-book"><b>Upcoming Bookings</b></h1>
        <h1 class="past-book"><b>Past Bookings</b></h1>
    </div>
	
    <div class="h-content">
        <div class="scrollboxl">
			<?php
				if($type == 'admin')
				{
					$sql = "SELECT * FROM meetings
							WHERE CONCAT('book_time', ' ', 'book_date') >= DATE_FORMAT(NOW(), '%H:%i %d/%m/%Y')
							AND book_id IN 
							(
								SELECT book_id
								FROM bookings
							)";
					$result = $conn->query($sql);
					
					if($result->num_rows > 0)
					{
						echo "<table class=book-table>
								<tr>
									<th id=hid>Booking ID</th>
									<th id=htime>Time</th>
									<th id=hdate>Date</th>
									<th id=hplace>Location</th>
								</tr>";
						
						// output data from each row
						while($row = $result->fetch_assoc())
						{
							echo "<tr><td id=bid>".$row["book_id"].
								"</td><td id=btime>".$row["book_time"].
								"</td><td id=bdate>".$row["book_date"].
								"</td><td id=bplace>".$row["book_place"].
								"</td></tr>";
						}
						echo "</table>";
					}
					else
					{
						echo "No upcoming bookings.";
					}
				}
				else
				{
					$sql = "SELECT * FROM meetings 
							WHERE CONCAT('book_time', ' ', 'book_date') >= DATE_FORMAT(NOW(), '%H:%i %d/%m/%Y')
							AND book_id IN 
							(
								SELECT book_id
								FROM bookings
								WHERE murdoch_id = '$murdID'
							)";
					$result = $conn->query($sql);
					
					if($result->num_rows > 0)
					{
						echo "<form action='' method='post'>
								<table class=book-table>
									<tr>
										<th id=hid>Booking ID</th>
										<th id=htime>Time</th>
										<th id=hdate>Date</th>
										<th id=hplace>Location</th>
										<th id=hplace>Action</th>
									</tr>";
						
						// output data from each row
						while($row = $result->fetch_assoc())
						{
							echo "<tr><td id=bid>".$row["book_id"].
								"</td><td id=btime>".$row["book_time"].
								"</td><td id=bdate>".$row["book_date"].
								"</td><td id=bplace>".$row["book_place"].
								"</td><td><button class='btn btn-danger btn-xs' id='delete' type='submit' name='deleteItem' value=".$row['book_id'].">Delete</button></td></tr>";
						}
						echo "</table>
							  </form>";
					}
					else
					{
						echo "No upcoming bookings.";
					}

				}
			?>
		</div>
		
		<div class="scrollboxr">
			<?php
				if($type == 'admin')
				{
					$sql = "SELECT * FROM meetings
							WHERE CONCAT('book_time', ' ', 'book_date') < DATE_FORMAT(NOW(), '%H:%i %d/%m/%Y')
							AND book_id IN 
							(
								SELECT book_id
								FROM bookings
							)";
					$result = $conn->query($sql);
					
					if($result->num_rows > 0)
					{
						echo "<table class=book-table>
								<tr>
									<th id=hid>Booking ID</th>
									<th id=htime>Time</th>
									<th id=hdate>Date</th>
									<th id=hplace>Location</th>
								</tr>";
						
						// output data from each row
						while($row = $result->fetch_assoc())
						{
							echo "<tr><td id=bid>".$row["book_id"].
								"</td><td id=btime>".$row["book_time"].
								"</td><td id=bdate>".$row["book_date"].
								"</td><td id=bplace>".$row["book_place"].
								"</td></tr>";
						}
						echo "</table>";
					}
					else
					{
						echo "No upcoming bookings.";
					}
				}
				else
				{
					$sql = "SELECT * FROM meetings 
							WHERE CONCAT('book_time', ' ', 'book_date') < DATE_FORMAT(NOW(), '%H:%i %d/%m/%Y')
							AND 'book_id' IN 
							(
								SELECT book_id
								FROM bookings
								WHERE murdoch_id = '$murdID'
							)";
					$result = $conn->query($sql);
					
					if($result->num_rows > 0)
					{
						echo "<table class=book-table>
								<tr>
									<th id=hid>Booking ID</th>
									<th id=htime>Time</th>
									<th id=hdate>Date</th>
									<th id=hplace>Location</th>
								</tr>";
						
						// output data from each row
						while($row = $result->fetch_assoc())
						{
							echo "<tr><td id=bid>".$row["book_id"].
								"</td><td id=btime>".$row["book_time"].
								"</td><td id=bdate>".$row["book_date"].
								"</td><td id=bplace>".$row["book_place"].
								"</td></tr>";
						}
						echo "</table>";
					}
					else
					{
						echo "No past bookings.";
					}

				}
			?>
		</div>
    </div>

	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script language="JavaScript" type="text/javascript">
	$(document).ready(function()
	{
		$("#delete").click(function(e)
		{
			if(!confirm('Are you sure you want to delete the booking?'))
			{
				e.preventDefault();
				return false;
			}
			return true;
		});
	});
	</script>

    <footer>
        <a href="https://www.murdoch.edu.au/"><img src="../images/footer-logo.png" class="MurdochLogo"></a>

        <ul>
            <li><a href="https://www.murdoch.edu.au/notices/copyright-disclaimer">Copyright & Disclaimer</a></li>
            <li><a href="http://goto.murdoch.edu.au/Privacy">Privacy</a></li>
            <li><a href="https://www.murdoch.edu.au/contact-us">Contact Us</a></li>
            <li><a href="https://www.murdoch.edu.au/">© 2022 Murdoch University</a></li>
        </ul>
    </footer>

</body>
</html>