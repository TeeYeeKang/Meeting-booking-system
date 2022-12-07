<?php
	session_start();

	if (!isset($_SESSION["Murdoch_ID"]))
	{
		header("Location:../pages/Login.php");
	}
    $conn = mysqli_connect('localhost', 'root', '', 'meetme') or die ('Unable to connect');
	$mysqli = new mysqli('localhost', 'root', '', 'meetme');
	$type = $_SESSION['Murdoch_ID']['type'];
	$murdID = $_SESSION['Murdoch_ID']['murdoch_id'];
	$murdEmail = $_SESSION['Murdoch_ID']['murdoch_email'];
	
	use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    require '../pages/phpmailer/src/PHPMailer.php';
    require '../pages/phpmailer/src/SMTP.php';
    require '../pages/phpmailer/src/Exception.php';
    require '../pages/includes/dbh.inc.php';
	
	if(isset($_GET['date']))
	{
		$date = $_GET['date'];
		$stmt = $mysqli->prepare("select * from bookings where book_date = ?");
		$stmt->bind_param('s', $date);
		$bookings = array();
		if($stmt->execute())
		{
			$result = $stmt->get_result();
			if($result->num_rows>0)
			{
				while($row = $result->fetch_assoc())
				{
					$bookings[] = $row['book_time'];
				}
				$stmt->close();
			}
		}
	}
	
	if(isset($_POST['submitBooking']))
	{
		$book_id = $_POST['bookid'];
		$murdoch_id = $_POST['murdoch_id'];
		$timeslot = $_POST['timeslot'];
		$meeting_date = $_POST['date'];
		$location = $_POST['location'];
		
		$stmt = $mysqli->prepare("select * from bookings where book_date = ? AND book_time = ?");
		$stmt->bind_param('ss', $date, $timeslot);
		if($stmt->execute())
		{
			$result = $stmt->get_result();
			if($result->num_rows>0)
			{
				$msg = "<div class='alert alert-danger'>Already booked.</div>";
			}
			else
			{
				$stmt = $mysqli->prepare("INSERT INTO bookings (murdoch_id, book_id, book_date, book_time) VALUES (?,?,?,?)");
				$stmt->bind_param('ssss', $murdoch_id, $book_id, $date, $timeslot);
				$stmt->execute();
				$msg = "<div class='alert alert-success'>Booking Successful.</div>";
				$bookings[]=$timeslot;
				$stmt->close();
				$mysqli->close();
			}
		}
		
		$time = preg_split("/[-]+/", $timeslot);

		$username = NULL;
		$to = NULL;
		$teacher_id = NULL;

        // send confirmation email with outlook calendar
		$mail= new PHPMailer();  
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'ict302meetme@gmail.com';
		$mail->Password = 'xyaascvkduakovsi';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->CharSet = 'UTF-8';
		$mail->ContentType = 'text/calendar';
		$mail->isHTML(true);
		$mail->setFrom('ict302meetme@gmail.com');

		//retrieve student email by using murdoch id
		$sql = "SELECT murdoch_email FROM useraccount WHERE murdoch_id = '$murdoch_id'" ;
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
					$to = $row["murdoch_email"];
					$mail->addAddress($to);
			}
		}

		//retrieve student name
		$sql_name = "SELECT username FROM useraccount WHERE murdoch_id = '$murdoch_id'" ;
		$result_name = $conn->query($sql_name);
		if($result_name->num_rows > 0){
			while($row_name = $result_name->fetch_assoc()){
				$username = $row_name['username'];
			}
		}

		// retrieve teacher email 
		$sql_teacher_id = "SELECT teacher_id FROM meetings WHERE book_id = '$book_id'";
		$result_teacher = $conn->query($sql_teacher_id);
		if($result_teacher->num_rows > 0){
			while($row_teacher = $result_teacher->fetch_assoc()){
				$teacher_id = $row_teacher['teacher_id'];
			}
		}
		$sql_teacher_email = "SELECT murdoch_email FROM useraccount WHERE murdoch_id = '$teacher_id'";
		$result_teacher_email = $conn->query($sql_teacher_email);
		if($result_teacher_email->num_rows > 0){
			while($row_teacher_email = $result_teacher_email->fetch_assoc()){
					$to_teacher = $row_teacher_email["murdoch_email"];
					$mail->addAddress($to_teacher);
			}
		}	
		
		$from_name = "Murdoch";        
		$from_address = "ict302meetme@gmail.com";      
		$to_name = $username;                       
		$to_address = $to;  
		$startTime = $meeting_date . " " . $time[0];//"12/1/2022 12:00:00";  
		$endTime = $meeting_date . " " . $time[1]; //"12/1/2022 12:30:00";    
		$subject = "Reminder - Meeting Confirmation";   
		$description = "You have successfully booked a meeting.";    
		$domain = 'gmail.com';

		//Create Email Headers
		$mime_boundary = "----Meeting Booking----".MD5(TIME());

		$headers = "From: ".$from_name." <".$from_address.">\n";
		$headers .= "Reply-To: ".$to_name." <".$to_address.">\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
		$headers .= "Content-class: urn:content-classes:calendarmessage\n";

		//Event setting
		$ical = 'BEGIN:VCALENDAR' . "\r\n" .
		'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
		'VERSION:2.0' . "\r\n" .
		'METHOD:REQUEST' . "\r\n" .
		'BEGIN:VTIMEZONE' . "\r\n" .
		'TZID:Eastern Time' . "\r\n" .
		'BEGIN:STANDARD' . "\r\n" .
		'DTSTART:20091101T020000' . "\r\n" .
		'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
		'TZOFFSETFROM:-0400' . "\r\n" .
		'TZOFFSETTO:-0500' . "\r\n" .
		'TZNAME:EST' . "\r\n" .
		'END:STANDARD' . "\r\n" .
		'BEGIN:DAYLIGHT' . "\r\n" .
		'DTSTART:20090301T020000' . "\r\n" .
		'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
		'TZOFFSETFROM:-0500' . "\r\n" .
		'TZOFFSETTO:-0400' . "\r\n" .
		'TZNAME:EDST' . "\r\n" .
		'END:DAYLIGHT' . "\r\n" .
		'END:VTIMEZONE' . "\r\n" .	
		'BEGIN:VEVENT' . "\r\n" .
		'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
		'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$to_address. "\r\n" .
		'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
		'UID:'.date("Ymd\TGis", strtotime($startTime)).rand()."@".$domain."\r\n" .
		'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
		'DTSTART;TZID="Pacific Daylight":'.date("Ymd\THis", strtotime($startTime)). "\r\n" .
		'DTEND;TZID="Pacific Daylight":'.date("Ymd\THis", strtotime($endTime)). "\r\n" .
		'TRANSP:OPAQUE'. "\r\n" .
		'SEQUENCE:1'. "\r\n" .
		'SUMMARY:' . $subject . "\r\n" .
		'LOCATION:' . $location . "\r\n" .
		'CLASS:PUBLIC'. "\r\n" .
		'PRIORITY:5'. "\r\n" .
		'BEGIN:VALARM' . "\r\n" .
		'TRIGGER:-PT15M' . "\r\n" .
		'ACTION:DISPLAY' . "\r\n" .
		'DESCRIPTION:Reminder' . "\r\n" .
		'END:VALARM' . "\r\n" .
		'END:VEVENT'. "\r\n" .
		'END:VCALENDAR'. "\r\n";
	
		$mail->Subject = $subject;
		$mail->header = $headers;
		$message = 'Please find the attachment Outlook file and add the meeting to your Outlook calendar.';
		$mail->Body = $message;

		$mail->addStringAttachment($ical,'Booking_Confirmation.ics','base64','text/calendar');
		$mail->send();
	}

?>

<!doctype html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeetMe - Time Slots</title>
    <link rel="stylesheet" href="../book.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  </head>
  
  <body>
    <nav>
        <a class="logo" href="../index.php">MEET ME</a>
        <ul>
          <li><a href="home.php">Home</a></li>
          <li><a class="active" href="bookings.php">Bookings</a></li>
          <?php if($type == 'admin'){ ?>
		  <li><a href="upload.php">Upload</a></li>
		  <?php } ?>
          <li><a href="Logout.php">Log Out</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="text-center">Available Bookings for: <?php echo date('d-m-Y', strtotime($date)); ?></h1><hr>
        <div class="row">
		 <div class="col-md-12">
			<?php echo isset($msg)?$msg:"";?>
		 </div>
            <?php
				$sql = "SELECT book_id, book_time, book_date, book_place FROM meetings WHERE student_id = '$murdID' AND book_date = DATE_FORMAT('$date', '%d/%m/%Y')";
				$result = $conn->query($sql);
				
				if($result->num_rows > 0)
				{
					while($row = $result->fetch_assoc())
					{
						$id = $row["book_id"];
						$place = $row["book_place"];
						$time = $row["book_time"];
            ?>
				<div class="col-md-2">
					<div class="form-group">
						<?php if(in_array($time, $bookings)){ ?>
							<button class="btn btn-danger btn-sm">
								<?php echo $place." ".$time; ?>
							</button>
						<?php }else{ ?>
							<button class="btn btn-success book btn-sm"
								iddata="<?php echo $id; ?>"
								datedata="<?php echo date('d-m-Y', strtotime($date)); ?>"
								placedata="<?php echo $place; ?>"
								timedata="<?php echo $time; ?>">
								<?php echo $place." ".$time; ?>
							</button>
						<?php } ?>
						
					</div>
				</div>
			<?php } }
			else
			{
				echo "<p>No timeslots available.</p>";
			} ?>
        </div>
    </div>
	
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Booking</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form action="" method="post">
								<div class="form-group">
									<label for="" hidden>Book ID</label>
									<input required type="hidden" readonly name="bookid" id="bookid" class="form-control">
								</div>
								<div class="form-group">
									<label for="">Date</label>
									<input required type="text" readonly name="date" id="date" class="form-control">
								</div>
								<div class="form-group">
									<label for="">Timeslot</label>
									<input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
								</div>
								<div class="form-group">
									<label for="">Location</label>
									<input required type="text" readonly name="location" id="location" class="form-control">
								</div>
								<div class="form-group">
									<input type="hidden" name="murdoch_id" value = "<?php echo "$murdID";?>"/> 
								</div>
								<div class="form-group pull-right">
									<button class="btn btn-primary" type="submit" name="submitBooking">Book</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script>
		$(".book").click(function()
		{
			var id = $(this).attr('iddata');
			var date = $(this).attr('datedata');
			var place = $(this).attr('placedata');
			var timeslot = $(this).attr('timedata');
			$("#bookid").val(id);
			$("#date").val(date);
			$("#timeslot").val(timeslot);
			$("#location").val(place);
			$("#myModal").modal("show");
		})
	</script>
  </body>

    <div class="footer">
        <a href="https://www.murdoch.edu.au/"><img src="../images/footer-logo.png" class="MurdochLogo"></a>

        <ul>
            <li><a href="https://www.murdoch.edu.au/notices/copyright-disclaimer">Copyright & Disclaimer</a></li>
            <li><a href="http://goto.murdoch.edu.au/Privacy">Privacy</a></li>
            <li><a href="https://www.murdoch.edu.au/contact-us">Contact Us</a></li>
            <li><a href="https://www.murdoch.edu.au/">© 2022 Murdoch University</a></li>
        </ul>
	</div>
	
</html>