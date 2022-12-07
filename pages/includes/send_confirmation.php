<?php

	use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    require '../phpmailer/src/PHPMailer.php';
    require '../phpmailer/src/SMTP.php';
    require '../phpmailer/src/Exception.php';
    require 'dbh.inc.php';

if(isset($_POST['submitBooking'])){
		$book_id = $_POST['bookid'];
		$murdoch_id = $_POST['murdoch_id'];
		$timeslot = $_POST['timeslot'];
		$meeting_date = $_POST['date'];
		$location = $_POST['location'];

		//split start time and end time
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

		// retrive teacher email 
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
					$to_teacher = $row_teacher_email["Murdoch_Email"];
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

	header("Location: ../book.php");
?>