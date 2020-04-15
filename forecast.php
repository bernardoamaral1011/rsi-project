#!/usr/bin/php -q
<?php
# change this to match the code of your particular city
$weatherURL="http://pda.ipma.pt";
# don't let this script run for more than 60 seconds
set_time_limit(60);
# turn off output buffering
ob_implicit_flush(false);
# turn off error reporting, as it will most likely interfere with
# the AGI interface
error_reporting(0);
# create file handles if needed
if (!defined('STDIN'))
{
 define('STDIN', fopen('php://stdin', 'r'));
}
if (!defined('STDOUT'))
{
 define('STDOUT', fopen('php://stdout', 'w'));
}
if (!defined('STDERR'))
{
 define('STDERR', fopen('php://stderr', 'w'));
}
# retrieve all AGI variables from Asterisk
while (!feof(STDIN))
{
 $temp = trim(fgets(STDIN,4096));
 if (($temp == "") || ($temp == "\n"))
 {
 break;
 }
 $s = split(":",$temp);
 $name = str_replace("agi_","",$s[0]);
 $agi[$name] = trim($s[1]);
}
# print all AGI variables for debugging purposes
foreach($agi as $key=>$value)
{
 fwrite(STDERR,"-- $key = $value\n");
 fflush(STDERR);
}
#retrieve this web page
$weatherPage=file_get_contents($weatherURL);
#grab temperature in Fahrenheit
if(preg_match('/<td class="left-homecontain1"><p class="text">Lisboa<\/p><\/td>/i', $weatherPage))
{
	if(preg_match('/<p class="t_max">&nbsp;([0-9]+)<em>ºC<\/em>&nbsp;<\/p>/i',$weatherPage,$tmax))
	{
		$maxTemp=$tmax[1];
	}
	if(preg_match('/<p class="t_min">&nbsp;([0-9]+)<em>ºC<\/em>&nbsp;<\/p>/i',$weatherPage,$tmin))
	{
		$minTemp=$tmin[1];
	}
	if(preg_match('<p class="text"><img src="/public/imagens/simbol_([0-9]+).gif" alt="simbolo tempo Lisboa" \/><\/p>/i
',$weatherPage,$wth))
	{
		$weather=$wth[1]-1;
	}
}

fwrite(STDOUT,"STREAM FILE maximum \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE temperature \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE is \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"SAY NUMBER $tmax \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE degrees \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);

fwrite(STDOUT,"STREAM FILE minimum \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE temperature \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE is \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"SAY NUMBER $tmin \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE degrees \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);

$wea = array("sunny" "sunny" "cloudy" "cloudy" "sunny" "rainy" "rainy" "rainy" "rainy" "rainy" "rainy" "rainy" "rainy" "rainy" "rainy" "foggy" "foggy" "rainy" "storm" "storm");

fwrite(STDOUT,"STREAM FILE weather \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE is \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);
fwrite(STDOUT,"STREAM FILE $wea[$weather] \"\"\n");
fflush(STDOUT);
$result = trim(fgets(STDIN,4096));
checkresult($result);

function checkresult($res)
{
 trim($res);
 if (preg_match('/^200/',$res))
 {
 if (! preg_match('/result=(-?\d+)/',$res,$matches))
 {
 fwrite(STDERR,"FAIL ($res)\n");
 fflush(STDERR);
 return 0;
 }
 else
 {
 fwrite(STDERR,"PASS (".$matches[1].")\n");
 fflush(STDERR);
 return $matches[1];
 }
 }
 else
 {
 fwrite(STDERR,"FAIL (unexpected result '$res')\n");
 fflush(STDERR);
 return -1;
 }
}
?>