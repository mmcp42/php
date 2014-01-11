<?php
//======================================================================
// wxGetRecord.php
//
// retrieves current data from database
//
// parameter:
//	wxid	identifies the station for record selection
//		id	string
//	period	indicates period for which records required
//		1	get 1 (most recent) record
//		H	get all records for most recent hour
//		D	get all records for most recent 24 hours
//		W	get all records for most recent week
//		M	get all records for most recent month
//		Y	get all records for most recent year
//	style	identifies result style
//		T	trend data (max and minimum)
//		A	averages by period
//======================================================================

// set up database information
// put your database connection information here
$host="hostname";
$user="username";
$password="password";
$database="database";

$debug = false;

// unpack passed parameters
$wxid = $_GET['wxid'];
$period = $_GET['period'];
$style = $_GET['style'];
if (empty($style))
	$style = 'A';
$debug = $_GET['debug'];

if ($debug)
{
	echo 'wxid: ' . $wxid . '<br>';
	echo 'period: ' . $period . '<br>';
	echo 'style: ' . $style . '<br>';
}

// connect to database
mysql_connect($host,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

if (1==1)
{
	// create the query and get current results
	$query="SELECT ts, timestamp, wind, rain, windDir, battery, temperatureR, humidity, temperatureH, pressure, temperatureP, dewpoint " .
			"from wx " .
			"where wxid='" . $wxid . "' " .
			"order by timestamp desc LIMIT 1";

	if ($debug)
		echo 'query: ' .$query . '<br>';

	$reply = mysql_query($query);
	if (!$reply)
		die('select failed: ' . mysql_error());

	// unpack the results
	if ($row = mysql_fetch_assoc($reply))
	{
		// unpack results
		$ts        = $row['ts'];
		$timestamp = $row['timestamp'];
		$rain      = $row['rain'];
		$wind      = $row['wind'];
		$windDir   = $row['windDir']      / 10.0;
		$battery   = $row['battery']      / 100.0;
		$tempR     = $row['temperatureR'] / 10.0;
		$humidity  = $row['humidity']     / 10.0;
		$tempH     = $row['temperatureH'] / 10.0;
		$pressure  = $row['pressure'];
		$tempP     = $row['temperatureP'] / 10.0;
		$dewpoint  = $row['dewpoint']     / 10.0;

		if ($style=='T')
		{
			// send values
			echo 'timestamp=' . $timestamp . ', ';
			echo 'ts='        . $ts        . ', ';
			echo 'rain='      . $rain      . ', ';
			echo 'wind='      . $wind      . ', ';
			echo 'winddir='   . $windDir   . ', ';
			echo 'battery='   . $battery   . ', ';
			echo 'tempR='     . $tempR     . ', ';
			echo 'humidity='  . $humidity  . ', ';
			echo 'tempH='     . $tempH     . ', ';
			echo 'pressure='  . $pressure  . ', ';
			echo 'tempP='     . $tempP     . ', ';
			echo 'dewpoint='  . $dewpoint  . ',;';
			echo '<br>';
		}
	}
	else
		die('database read failed');
}

// generate filter for period
if ($debug)
	echo 'getFilter<br>';
$filter = getFilter($timestamp, $ts, $period);
if ($debug)
	echo 'filter: ' . $filter . '<br>';

if ($filter != '' && $style == 'T' )
{
	// show trend data (minimum and maximum)
	
	// get min
	$query = "SELECT min(wind), min(rain), min(windDir), min(battery), min(temperatureR), min(humidity), min(temperatureH), min(pressure), min(temperatureP) " .
		", min(dewpoint) " .
		"from wx " .
		"where wxid='" . $wxid . "' " .
		"and " . $filter . " " .
		"order by ts desc ";

	if ($debug)
		echo 'sql: ' . $query . '<br>';

	$reply = mysql_query($query);
	if (!$reply)
		die('select failed: ' . mysql_error());
	if($row = mysql_fetch_assoc($reply))
	{
		// unpack results
		$rain      = $row['min(rain)'];
		$wind      = $row['min(wind)'];
		$windDir   = $row['min(windDir)']      / 10.0;
		$battery   = $row['min(battery)']      / 100.0;
		$tempR     = $row['min(temperatureR)'] / 10.0;
		$humidity  = $row['min(humidity)']     / 10.0;
		$tempH     = $row['min(temperatureH)'] / 10.0;
		$pressure  = $row['min(pressure)'];
		$tempP     = $row['min(temperatureP)'] / 10.0;
		$dewPoint  = $row['min(dewpoint)']     / 10.0;

		// send values
		echo 'minrain='      . $rain      . ', ';
		echo 'minwind='      . $wind      . ', ';
		echo 'minwinddir='   . $windDir   . ', ';
		echo 'minbattery='   . $battery   . ', ';
		echo 'mintempR='     . $tempR     . ', ';
		echo 'minhumidity='  . $humidity  . ', ';
		echo 'mintempH='     . $tempH     . ', ';
		echo 'minpressure='  . $pressure  . ', ';
		echo 'mintempP='     . $tempP     . ', ';
		echo 'mindewpoint='  . $dewPoint  . ',;';
		echo '<br>';
	}

		// get max
	$query = "SELECT max(wind), max(rain), max(windDir), max(battery), max(temperatureR), max(humidity), max(temperatureH), max(pressure), max(temperatureP) " .
		", max(dewpoint) " .
		"from wx " .
		"where wxid='" . $wxid . "' " .
		"and " . $filter . " " .
		"order by ts desc ";

	if ($debug)
		echo 'sql: ' . $query . '<br>';

	$reply = mysql_query($query);
	if (!$reply)
		die('select failed: ' . mysql_error());
	if($row = mysql_fetch_assoc($reply))
	{
		// unpack results
		$rain      = $row['max(rain)'];
		$wind      = $row['max(wind)'];
		$windDir   = $row['max(windDir)']      / 10.0;
		$battery   = $row['max(battery)']      / 100.0;
		$tempR     = $row['max(temperatureR)'] / 10.0;
		$humidity  = $row['max(humidity)']     / 10.0;
		$tempH     = $row['max(temperatureH)'] / 10.0;
		$pressure  = $row['max(pressure)'];
		$tempP     = $row['max(temperatureP)'] / 10.0;
		$dewPoint  = $row['max(dewpoint)']     / 10.0;

		// send values
		echo 'maxrain='      . $rain      . ', ';
		echo 'maxwind='      . $wind      . ', ';
		echo 'maxwinddir='   . $windDir   . ', ';
		echo 'maxbattery='   . $battery   . ', ';
		echo 'maxtempR='     . $tempR     . ', ';
		echo 'maxhumidity='  . $humidity  . ', ';
		echo 'maxtempH='     . $tempH     . ', ';
		echo 'maxpressure='  . $pressure  . ', ';
		echo 'maxtempP='     . $tempP     . ', ';
		echo 'maxdewpoint='  . $dewPoint  . ',;';
		echo '<br>';
	}
}

if ($debug)
	echo 'getGroupBy<br>';
$groupBy = getGroupBy($ts, $period);
if ($debug)
	echo 'groupBy: ' . $groupBy . '<br>';

// get average result sets
if ($filter != '' && $style == 'A')
{
	// get more results
	
	// create the query and get current results
	$query="SELECT " . $groupBy . " as timestamp, avg(wind) as avgwind, avg(rain) as avgrain, avg(windDir) as avgwinddir, avg(battery) as avgbattery, avg(temperatureR) as avgtemperatureR, " .
		"avg(humidity) as avghumidity, avg(temperatureH) as avgtemperatureH, avg(pressure) as avgpressure, avg(temperatureP) as avgtemperatureP, avg(dewpoint) as avgdewpoint " .
		"from wx " .
		"where wxid='" . $wxid . "' " .
		"and " . $filter . " " .
		"group by " . $groupBy . " " .
		"order by " . $groupBy;

	if ($debug)
		echo 'sql: ' . $query . '<br>';

	$reply = mysql_query($query);
	if (!$reply)
		die('select failed: ' . mysql_error());
	while($row = mysql_fetch_assoc($reply))
	{
		// unpack results
		$timestamp = $row['timestamp'];
		$rain      = $row['avgrain'];
		$wind      = $row['avgwind'];
		$windDir   = $row['avgwinddir']      / 10.0;
		$battery   = $row['avgbattery']      / 100.0;
		$tempR     = $row['avgtemperatureR'] / 10.0;
		$humidity  = $row['avghumidity']     / 10.0;
		$tempH     = $row['avgtemperatureH'] / 10.0;
		$pressure  = $row['avgpressure'];
		$tempP     = $row['avgtemperatureP'] / 10.0;
		$dewPoint  = $row['avgdewpoint']     / 10.0;

		// send values
		echo 'timestamp=' . $timestamp        . ', ';
		echo 'rain='      . $rain      . ', ';
		echo 'wind='      . $wind      . ', ';
		echo 'winddir='   . $windDir   . ', ';
		echo 'battery='   . $battery   . ', ';
		echo 'tempR='     . $tempR     . ', ';
		echo 'humidity='  . $humidity  . ', ';
		echo 'tempH='     . $tempH     . ', ';
		echo 'pressure='  . $pressure  . ', ';
		echo 'tempP='     . $tempP     . ', ';
		echo 'dewpoint='  . $dewPoint  . ',;';
		echo '<br>';
	}
}

// finished with query
mysql_free_result($reply);

// finished with database
mysql_close();

// functions follow

function getGroupBy($timestamp, $period)
{
	// create group by for avg(data) query
	// SELECT substr(timestamp, 1, 13), avg(battery) FROM wx group by substr(timestamp, 1, 13) order by substr(timestamp, 1, 13)
	// timestamp is yyyy-mm-dd hh:nn:ss
	//              1234567890123456789
	
	switch ($period)
	{
		case '1':	
			// just one record so no filter
			$reply = ""; 
			break;
		case 'H':	
			// hour so display per minute
			$reply = "substr(timestamp, 1, 16)"; 
			break;
		case 'D':	
			// day so display per hour
			$reply = "substr(timestamp, 1, 13)"; 
			break;
		case 'W':	
			// week so display per hour
			$reply = "substr(timestamp, 1, 13)"; 
			break;
		case 'M':	
			// month so display per day
			$reply = "substr(timestamp, 1, 10)"; 
			break;
		case 'Y':	
			// year so display per day
			$reply = "substr(timestamp, 1, 10)"; 
			break;
	}
	if ($debug)
		echo 'groupBy: ' . $reply . '<br>';

	return $reply;
}

function getFilter($timestamp, $ts, $period)
{
	if ($debug)
		echo 'getFilter<br>';
	
	// split timestamp so we can use it in filter
	$d1 = explode(' ', $timestamp);
	$d2= explode('-', $d1[0]);
	$t = explode(':', $d1[1]);
	$year   = $d2[0];
	$month  = $d2[1];
	$day    = $d2[2];
	$hour   = $t[0];
	$minute = $t[1];
	$second = $t[2];

	if ($debug)
	{
		echo 'timestamp: ' . $timestamp . '<br>';
		echo 'year: ' . $year . '<br>';
		echo 'month: ' . $month . '<br>';
		echo 'day: ' . $day . '<br>';
		echo 'hour: ' . $hour . '<br>';
		echo 'minute: ' . $minute . '<br>';
		echo 'second: ' . $second . '<br>';
	}

	switch ($period)
	{
		case '1':	
			// just one record so no filter
			$reply = ""; 
			break;
		case 'H':	
			// calculate 1 hour earlier
			$delta = 1 * 60 * 60;
			$ts2 = $ts - $delta;
			$reply = "ts<=" . $ts . " AND ts>=" . $ts2; 
			break;
		case 'D':	
			// calculate 1 day earlier
			$delta = 24 * 60 * 60;
			$ts2 = $ts - $delta;
			$reply = "ts<=" . $ts . " AND ts>=" . $ts2; 
			break;
		case 'W':	
			// calculate 7 days earlier
			$delta = 7 * 24 * 60 * 60;
			$ts2 = $ts - $delta;
			$reply = "ts<=" . $ts . " AND ts>=" . $ts2; 
			break;
		case 'M':	
			// calculate 1 month earlier
			$month = $month - 1;
			if ($month <= 0)
			{
				$month = 12;
				$year = $year - 1;
			}
			$reply = "timestamp<='" . $ts . "' AND timestamp>='" . $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second . "'"; 
			break;
		case 'Y':	
			// calculate 1 year earlier
			$year = $year - 1;
			$reply = "timestamp<='" . $ts . "' AND timestamp>='" . $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second . "'"; 
			break;
	}
	if ($debug)
		echo 'filter: ' . $reply . '<br>';

	return $reply;
}
?>