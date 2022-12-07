<?php
	session_start();
	
	if (!isset($_SESSION["Murdoch_ID"]))
	{
		header("Location:../pages/Login.php");
	}
	$type = $_SESSION['Murdoch_ID']['type'];
	$murdID = $_SESSION['Murdoch_ID']['murdoch_id'];
	$murdEmail = $_SESSION['Murdoch_ID']['murdoch_email'];
	
	function build_calendar($month, $year) 
	{
		$mysqli = new mysqli('localhost', 'root', '', 'meetme');
		/*$stmt = $mysqli->prepare("select * from bookings where MONTH(booking_date) = ? AND YEAR(booking_date) = ?");
		$stmt->bind_param('ss', $month, $year);
		$bookings = array();
		if($stmt->execute())
		{
			$result = $stmt->get_result();
			if($result->num_rows>0)
			{
				while($row = $result->fetch_assoc())
				{
					$bookings[] = $row['booking_date'];
				}
				$stmt->close();
			}
		}*/
		
		$murdID = $_SESSION['Murdoch_ID']['murdoch_id'];
		$conn = mysqli_connect('localhost', 'root', '', 'meetme') or die ('Unable to connect');

		$sql = "SELECT book_id, book_time, book_date, book_place FROM meetings WHERE student_id = '$murdID'";
		$result = $conn->query($sql);
		$meetings = array();
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{
				$meetdate = $row['book_date'];
				$newdate = date("Y-d-m", strtotime($meetdate));
				$meetings[] = $newdate;
			}
		}
		
		// Create array containing abbreviations of days of week.
		$daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

		// What is the first day of the month in question?
		$firstDayOfMonth = mktime(0,0,0,$month,1,$year);

		// How many days does this month contain?
		$numberDays = date('t',$firstDayOfMonth);

		// Retrieve some information about the first day of the
		// month in question.
		$dateComponents = getdate($firstDayOfMonth);

		// What is the name of the month in question?
		$monthName = $dateComponents['month'];

		// What is the index value (0-6) of the first day of the
		// month in question.
		$dayOfWeek = $dateComponents['wday'];

		// Create the table tag opener and day headers
		$datetoday = date('Y-m-d');
		$calendar = "<table class='table table-bordered'>";
		$calendar .= "<center><h2>$monthName $year</h2>";
		$calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month-1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'>Previous Month</a> ";
		$calendar.= " <a class='btn btn-xs btn-primary' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a> ";
		$calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month+1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."'>Next Month</a></center><br>";
		$calendar .= "<tr>";

		// Create the calendar headers
		foreach($daysOfWeek as $day) {
			$calendar .= "<th  class='header'>$day</th>";
		} 

		// Create the rest of the calendar
		// Initiate the day counter, starting with the 1st.
		$currentDay = 1;
		$calendar .= "</tr><tr>";

		// The variable $dayOfWeek is used to
		// ensure that the calendar
		// display consists of exactly 7 columns.
		if ($dayOfWeek > 0) { 
			for($k=0;$k<$dayOfWeek;$k++){
				$calendar .= "<td  class='empty'></td>"; 
			}
		}
		
		$month = str_pad($month, 2, "0", STR_PAD_LEFT);
	  
		while ($currentDay <= $numberDays) {

			// Seventh column (Saturday) reached. Start a new row.
			if ($dayOfWeek == 7) {
				$dayOfWeek = 0;
				$calendar .= "</tr><tr>";
			}
			$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
			$date = "$year-$month-$currentDayRel";
			$dayname = strtolower(date('l', strtotime($date)));
			$eventNum = 0;
			$today = $date==date('Y-m-d')? "today" : "";
			if($date<date('Y-m-d')){
				$calendar.="<td><h4>$currentDay</h4> <button class='btn btn-default btn-xs'>N/A</button>";
			}elseif(in_array($date, $meetings)){
				if(checkSlots($mysqli, $date, $murdID))
				{
					$calendar.="<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>Booked</button>";
				}else
				{
					$calendar.="<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=".$date."' class='btn btn-success btn-xs'>Book</a>";
				}
			}else{
				$calendar.="<td><h4>$currentDay</h4> <button class='btn btn-default btn-xs'>N/A</button>";				
			}
			$calendar .="</td>";
			// Increment counters
			$currentDay++;
			$dayOfWeek++;
		}
		 
		// Complete the row of the last week in month, if necessary
		if ($dayOfWeek != 7) { 
			$remainingDays = 7 - $dayOfWeek;
			for($l=0;$l<$remainingDays;$l++){
				$calendar .= "<td class='empty'></td>"; 

			}
		}
		$calendar .= "</tr>";
		$calendar .= "</table>";
		echo $calendar;
	}
	
	function checkSlots($mysqli, $date, $murdID)
	{
		$stmt = $mysqli->prepare("SELECT * FROM meetings WHERE book_date = ? AND student_id = ?");
		$stmt->bind_param('ss', $date, $murdID);
		$meeting = 0;
		if($stmt->execute())
		{
			$result = $stmt->get_result();
			if($result->num_rows>0)
			{
				while($row = $result->fetch_assoc())
				{
					$meeting++;
				}
				$stmt->close();
			}
		}
		
		$stmt = $mysqli->prepare("SELECT * FROM bookings WHERE book_date = ? AND murdoch_id = ?");
		$stmt->bind_param('ss', $date, $murdID);
		$book = 0;
		if($stmt->execute())
		{
			$result = $stmt->get_result();
			if($result->num_rows>0)
			{
				while($row = $result->fetch_assoc())
				{
					$book++;
				}
				$stmt->close();
			}
		}
		
		if($book == $meeting)
		{
			return false;
		}else if($book < $meeting)
		{
			return true;
		}
		else
		{
			return true;
		}
	}
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeetMe - Bookings</title>
    <link rel="stylesheet" href="../navstyle.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="../calendar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>

<body>
    <nav>
        <a class="logo" href="../index.php">MEET ME</a>
        <ul>
          <li><a href="home.php">Home</a></li>
          <li><a class="active" href="bookings.php">Bookings</a></li>
          <?php if($type== 'admin'){ ?>
		  <li><a href="upload.php">Upload</a></li>
		  <?php } ?>
          <li><a href="Logout.php">Log Out</a></li>
        </ul>
    </nav>

    <div class="calendar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $dateComponents = getdate();
                        if(isset($_GET['month']) && isset($_GET['year'])){
                            $month = $_GET['month']; 			     
                            $year = $_GET['year'];
                        }else{
                            $month = $dateComponents['mon']; 			     
                            $year = $dateComponents['year'];
                        }
                        echo build_calendar($month,$year);
                    ?>
                </div>
            </div>
        </div>
    </div>

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
