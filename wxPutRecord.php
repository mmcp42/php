<?php
//======================================================================
// wxPutRecord.php
//
// called to add data 
//======================================================================

// set up database information
// put your database connection information here
$host="hostname";
$user="username";
$password="password";
$database="database";

$debug = false;

// unpack passed parameters
$wxid      = $_GET['wxid'];		 if (empty($wxid)      && $wxid !=0)      die('wxid missing');
/**/
$timestamp = $_GET['timestamp']; if (empty($timestamp) && $timestamp !=0) die('timestamp missing');
$ts        = $_GET['ts'];		 if (empty($ts)        && $ts !=0)        die('ts missing');
$rain      = $_GET['rain'];		 if (empty($rain)      && $rain !=0)      die('rain missing');
$wind      = $_GET['wind'];		 if (empty($wind)      && $wind !=0)      die('wind missing');
$windDir   = $_GET['winddir'];	 if (empty($windDir)   && $windDir !=0)   die('windDir missing');
$battery   = $_GET['battery'];	 if (empty($battery)   && $battery !=0)   die('battery missing');
$tempR     = $_GET['tempR'];	 if (empty($tempR)     && $tempR !=0)     die('tempR missing');
$humidity  = $_GET['humidity'];	 if (empty($humidity)  && $humidity !=0)  die('humidity missing');
$tempH     = $_GET['tempH'];	 if (empty($tempH)     && $tempH !=0)     die('tempH missing');
$pressure  = $_GET['pressure'];	 if (empty($pressure)  && $pressure !=0)  die('pressure missing');
$tempP     = $_GET['tempP'];	 if (empty($tempP)     && $tempP !=0)     die('tempP missing');
$dewpoint  = $_GET['dewpoint'];	 if (empty($dewpoint)  && $dewpoint !=0)  die('dewpoint missing');
/* */
$debug = $_GET['debug'];

if ($debug)
{
	echo 'wxid: '      . $wxid . '<br>';
	echo 'timestamp: ' . $timestamp . '<br>';
	echo 'ts: '        . $ts . '<br>';
	echo 'rain: '      . $rain . '<br>';
	echo 'wind: '      . $wind . '<br>';
	echo 'windDir: '   . $windDir . '<br>';
	echo 'battery: '   . $battery . '<br>';
	echo 'tempR: '     . $tempR . '<br>';
	echo 'humidity: '  . $humidity . '<br>';
	echo 'tempH: '     . $tempH . '<br>';
	echo 'pressure: '  . $pressure . '<br>';
	echo 'tempP: '     . $tempP . '<br>';
	echo 'dewpoint: '  . $dewpoint . '<br>';
}

// connect to database
mysql_connect($host,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

// create the query and get current results
$query="INSERT INTO wx (wxid, timestamp, ts, wind, rain, windDir, battery, temperatureR, humidity, temperatureH, pressure, temperatureP, dewpoint) " .
		"VALUES (" .
		"'" . $wxid . "', '" . $timestamp . "', '" . $ts . "', " . $wind . ", " . $rain . ", " . $windDir . ", " . $battery . ", " . $tempR . ", " . 
		$humidity . ", " . $tempH . ", " . $pressure . ", " . $tempP . ", " . $dewpoint .
		")";

if ($debug)
	echo 'query: ' .$query . '<br>';

$reply = mysql_query($query);
if (!$reply)
	die('select failed: ' . mysql_error());

// let caller know it worked
echo 'ok';
?>