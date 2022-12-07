<?php
	session_start();
	
	if (!isset($_SESSION["Murdoch_ID"]))
	{
		header("Location:../pages/Login.php");
	}
	$type = $_SESSION['Murdoch_ID']['type'];
    require 'includes/dbh.inc.php';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
    <link rel="stylesheet" href="../navstyle.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../upload.css?v=<?php echo time(); ?>">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
	<script src="https://code.jquery.com/jquery-latest.min.js"></script>
	<script>
   		$(document).ready(function(){
   		window.history.replaceState("","",window.location.href)
   	});
	</script>
</head>

<body>
    <nav>
        <a class="logo" href="../index.php">MEET ME</a>
        <ul>
          <li><a href="home.php">Home</a></li>
          <li><a href="bookings.php">Bookings</a></li>
		  <?php if($type== 'admin'){ ?>
			<li><a class="active" href="upload.php">Upload</a></li>
		  <?php } ?>
          <li><a href="Logout.php">Log Out</a></li>
        </ul>
    </nav>

    <div class="upload-zone">
        <form action="" method="POST" enctype="multipart/form-data">
        <div class="drop-zone">
        <span class="drop-zone__prompt">Drop file here or click to upload</span>
            <input class="drop-zone__input" type="file" name="excel" required value="">
        </div>
        <button class="button-40" type="submit" name="import" onclick="checker()">Upload</button>
        </form>
    </div>

	<div class="buttons">
		<form action="" method="POST">
			<button class="button-40" type="submit" name="removeMeetings" onclick="checker()">Clear Meetings</button>
		</form>
		<form action="" method="POST">
			<button class="button-40" type="submit" name="removeBookings" onclick="checker()">Clear Bookings</button>
		</form>
		<form action="" method="POST">
			<button class="button-40" type="submit" name="removeBookingsMeetings" onclick="checker()">Clear Bookings and Meetings</button>
		</form>
	</div>


    <div class="booking">
    <table border=1>
			<tr>
				<td>Row</td>
				<td>Booking ID</td>
				<td>Book Time</td>
                <td>Book Date</td>
				<td>Book Place</td>
			</tr>
			<?php
			$i = 1;
			$rows = mysqli_query($conn, "SELECT * FROM meetings");
			foreach($rows as $row) :
			?>
			<tr>
				<td> <?php echo $i++; ?> </td>
				<td> <?php echo $row["book_id"]; ?> </td>
				<td> <?php echo $row["book_time"]; ?> </td>
				<td> <?php echo $row["book_date"]; ?> </td>
                <td> <?php echo $row["book_place"]; ?> </td>
			</tr>
			<?php endforeach; ?>
		</table>
    </div>

    <div class="booking">
    <table border=1>
			<tr>
                <td>Row</td>
				<td>Emails</td>
			</tr>
			<?php
			$i = 1;
			$arr = array();
			$rows = mysqli_query($conn, "SELECT student_email FROM meetings WHERE student_email !=' ' ");
			foreach($rows as $row) :
				if(!in_array(($row["student_email"]), $arr)){
				?>
				<tr>
					<td> <?php echo $i++; ?> </td>
					<td> <?php echo $row["student_email"]; ?> </td>
				</tr>
				<?php 
					array_push($arr, $row["student_email"]);
				}
				endforeach; ?>
		</table>
    </div>

    <form class="send_email" action = "includes/send_booking_email.php" method="POST">
        <button class="button-40" type="submit" name="sendEmail" onclick="checker()" >Send Bookings</button>
    </form>

    <footer>
        <a href="https://www.murdoch.edu.au/"><img src="../images/footer-logo.png" class="MurdochLogo"></a>

        <ul>
            <li><a href="https://www.murdoch.edu.au/notices/copyright-disclaimer">Copyright & Disclaimer</a></li>
            <li><a href="http://goto.murdoch.edu.au/Privacy">Privacy</a></li>
            <li><a href="https://www.murdoch.edu.au/contact-us">Contact Us</a></li>
            <li><a href="https://www.murdoch.edu.au/">© 2022 Murdoch University</a></li>
        </ul>
    </footer>

    <script src="../upload.js"></script>
	<script>
		function checker(){
			var result = confirm('Are you sure?');
			if(result == false) {
				event.preventDefault();
			}
		}
	</script>

    <?php
		if(isset($_POST["import"])){
			$fileName = $_FILES["excel"]["name"];
			$fileExtension = explode('.', $fileName);
            $fileExtension = strtolower(end($fileExtension));
			$newFileName = date("Y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;

			$targetDirectory = "../uploads/" . $newFileName;
			move_uploaded_file($_FILES['excel']['tmp_name'], $targetDirectory);

			error_reporting(0);
			ini_set('display_errors', 0);

			require 'excelReader/excel_reader2.php';
			require 'excelReader/SpreadsheetReader.php';

			$reader = new SpreadsheetReader($targetDirectory);
			foreach($reader as $key => $row){
				$book_id = $row[0];
				$book_time = $row[1];
                $book_date = $row[2];
                $book_place = $row[3];
				$teacher_id = $row[4];
				$student_email = $row[5];
				$student_id = $row[6];
				mysqli_query($conn, "INSERT INTO meetings VALUES('$book_id', '$book_time', '$book_date', '$book_place', '$teacher_id', '$student_email','$student_id')");
			}
			echo "<script>
			alert('Succesfully Imported.\\n Please confirm the booking details and student emails to be sent');
			document.location.href = '';
			</script>";
		}

		if(isset($_POST["removeMeetings"])){
			mysqli_query($conn, "DELETE FROM meetings");
			echo "<script>alert('All meetings have been deleted.')</script>";
			echo "<meta http-equiv='refresh' content='0'>";
		}
		if(isset($_POST["removeBookings"])){
			mysqli_query($conn, "DELETE FROM bookings");
			echo "<script>alert('All bookings have been deleted.')</script>";
			echo "<meta http-equiv='refresh' content='0'>";
		}
		if(isset($_POST["removeBookingsMeetings"])){
			mysqli_query($conn, "DELETE FROM meetings");
			mysqli_query($conn, "DELETE FROM bookings");
			echo "<script>alert('All meetings and bookings have been deleted.')</script>";
			echo "<meta http-equiv='refresh' content='0'>";
		}
	?>

	<?php
        if(isset($_GET["emailSend"])){
            if($_GET["emailSend"] == "success"){
                echo "<script>alert('Succesfully sent emails')</script>";
            } else {
				echo "<script>alert('Sending emails failed')</script>";
			}
			echo "<meta http-equiv='refresh' content='0;url=http://localhost/meetme/pages/upload.php'>";
        }
    ?>

</body>
</html>