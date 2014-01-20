<?php

//=======================================
//
// author	Mike McPherson
// version	1.01
// date		9th Dec 2013
//
// wxftp2sql script
//
// reads uploaded ftp files
// parses
// writes data to sql database
// moves the file to /processed folder
//
//=======================================

// navigate to the data folder
chdir('../../wxftp');

// get the filenames
$list = glob('*.wxdat');

// set up database information
// put your database connection information here
$host="hostname";
$user="username";
$password="password";
$database="database";

mysql_connect($host,$user,$password);
@mysql_select_db($database) or die( 'Unable to select database');

for ($i=0; $i<sizeof($list); $i++)
{
	// get the next file name
	$fullname = $list[$i];
	
	// extract wxid from the filename
	$filename = basename($fullname, '.wxdat');
	$p = strpos($filename, '.');
	$wxid = substr($filename, 0, $p);
	
	if (!file_exists($fullname))
	{
		// echo $fullname . ' not found<br>';
	}
	else
	{	
		// open the file
		$file = fopen($fullname, 'rb') or die( $fullname . 'file open failed');
		//echo $filename . '<br>';


		// read and unpack the header
		{
			$raw = fread($file, 4); $raw2 = unpack('V', $raw); $stamp = $raw2[1]; //echo 'stamp: ' . $stamp    . '<br>';	// timestamp
			$raw = fread($file, 2); $raw2 = unpack('v', $raw); $page  = $raw2[1]; //echo ' page: ' . $page  . '<br>';	// page
			$raw = fread($file, 4); $raw2 = unpack('V', $raw); $dtype = $raw2[1]; //echo 'dtype: ' . $dtype . '<br>';	// data type
			$magic = fread($file, 6);                                             //echo 'magic: ' . $magic . '<br>';	// magic string
			//echo '<br>';
		}

		// read records until eof
		$record = 0;
		while (!feof($file))
		{
			// read and unpack data record
			//echo 'record: ' . $record++ . '<br>';
			$raw = fread($file, 4); $raw2 = unpack('V', $raw);       $stamp = $raw2[1]; //echo '   timestamp: ' . $stamp . '<br>';		// timestamp
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);        $page = $raw2[1]; //echo '        page: ' . $page  . '<br>';		// page
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);        $rain = $raw2[1]; //echo '        rain: ' . $rain  . '<br>';		// rain
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);        $wind = $raw2[1]; //echo '        wind: ' . $wind  . '<br>';		// wind
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);    $windGust = $raw2[1]; //echo '    windGust: ' . $windGust  . '<br>';	// windGust
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);    $windLull = $raw2[1]; //echo '    windLull: ' . $windLull  . '<br>';	// windLull
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);     $windDir = $raw2[1]; //echo '     windDir: ' . $windDir  . '<br>';		// windDir
			$raw = fread($file, 2); $raw2 = unpack('v', $raw); $windDirGust = $raw2[1]; //echo ' windDirGust: ' . $windDirGust  . '<br>';	// windDirGust
			$raw = fread($file, 2); $raw2 = unpack('v', $raw); $windDirLull = $raw2[1]; //echo ' windDirLull: ' . $windDirLull  . '<br>';	// windDirLull
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);     $battery = $raw2[1]; //echo '     battery: ' . $battery  . '<br>';		// battery
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);       $tempR = $raw2[1]; //echo '       tempR: ' . $tempR  . '<br>';		// tempR
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);    $humidity = $raw2[1]; //echo '    humidity: ' . $humidity  . '<br>';	// humidity
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);       $tempH = $raw2[1]; //echo '       tempH: ' . $tempH  . '<br>';		// tempH
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);    $pressure = $raw2[1]; //echo '    pressure: ' . $pressure  . '<br>';	// pressure
			$raw = fread($file, 2); $raw2 = unpack('v', $raw);       $tempP = $raw2[1]; //echo '       tempP: ' . $tempP  . '<br>';		// tempH
			//echo '<br>';

			// calculate dewpoint
			$dewpoint = (($tempH/10.0)-(100.0 - $humidity/10.0)/5.0)*10;
			
			// create SQL
			if ($stamp != 0)
			{
				// convert $stamp to date/time format
				$ts = convertTS($stamp);
				// add record to wx table
				$sql = sprintf("INSERT INTO wx ( wxid, timestamp, ts, rain,
												wind, windGust, windLull, 
												windDir, windDirGust, windDirLull,
												battery, temperatureR, 
												humidity, temperatureH, 
												pressure, temperatureP, dewpoint)
												VALUES('%s', '%s', %u, %u, 
												%u, %u, %u, 
												%u, %u, %u, 
												%u, %u, 
												%u, %u, 
												%u, %u, %u)", 
												$wxid, $ts, $stamp, $rain, 
												$wind, $windGust, $windLull, 
												$windDir, $windDirGust, $windDirLull, 
												$battery, $tempR, 
												$humidity, $tempH, 
												$pressure, $tempP,
												$dewpoint);
				// echo 'sql: ' . $sql . '<br>';
				mysql_query($sql);

/*				
				// update timestamp 0 with latst results
				$sql = sprintf("UPDATE wx set 
								rain = %u, 
								wind = %u, 
								windGust = %u, 
								windLull = %u, 
								windDir = %u, 
								windDirGust = %u, 
								windDirLull = %u, 
								battery = %u, 
								temperatureR = %u, 
								humidity = %u, 
								temperatureH = %u, 
								pressure = %u, 
								temperatureP = %u 
								WHERE wxid='%s' and timestamp=0",
									$rain, 
									$wind, 
									$windGust, 
									$windLull, 
									$windDir, 
									$windDirGust, 
									$windDirLull, 
									$battery, 
									$tempR, 
									$humidity, 
									$tempH, 
									$pressure, 
									$tempP,
									$wxid);

//				echo 'sql: ' . $sql . '<br>';
				mysql_query($sql);				
*/

			}
		}
		
		// close the file
		fclose($file);
		
		// move it to procssed folder
		copy($fullname, 'processed/' . $fullname);
		unlink($fullname);
	}
}
mysql_close();

echo 'done<br>';
//echo 'stamp: ' . $stamp . '<br>';

function convertTS($t)
{
	// convert $t to date/time format

	if ($t==0)
		return 0;
		
	$daysInMonth = array ( 31,28,31,30,31,30,31,31,30,31,30,31 );
	
    $ss = $t % 60;
    $t = floor($t/60);
    $mm = $t % 60;
    $t = floor($t/60);
    $hh = $t % 24;
    $days = floor($t / 24);

	$yOff = 0;
	$leap = 0;
	$m = 1;

	while (1)
	{
		$leap = 0;
		if (($yOff % 4) == 0)
			$leap = 1;
        if ($days < 365 + $leap)
            break;
        $days = $days - (365 + $leap);
		$yOff = $yOff + 1;
    }
	while (1)
	{
        $daysPerMonth = $daysInMonth[$m-1];
        if ($leap AND $m == 2)
            $daysPerMonth = $daysPerMonth + 1;
        if ($days < $daysPerMonth)
            break;
        $days = $days - $daysPerMonth;
		$m = $m + 1;
    }
    $d = $days + 1;
	$reply =  '' . $yOff . '-' . $m . '-' . $d . ' ' . $hh . ':' . $mm . ':' . $ss;
	return $reply;
}

?>