<?php

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=index.ics');

$url = 'https://content.viaplay.fi/pcdash-fi/urheilu'; 
$data = file_get_contents($url); 
$objOrArray = json_decode($data, true); 


	
function traverse(&$objOrArray)
{

	$EPG=false;
	$synopsis=false;

	$title = "";
	$title_status = false;

	$synopsis = "";
	$synopsis_status = false;

	$streamStart = "";
	$streamStart_status = false;

	$streamEnd = "";
	$streamEnd_status = false;

	$x = 0;
	$xx = 0;
	global $xx, $x;

	global $EPG;
	global $synopsis;
	global $title;
	global $title_status;
	global $synopsis;
	global $synopsis_status;
	global $streamStart;
	global $streamStart_status;
	global $streamEnd;
	global $streamEnd_status;


	foreach ($objOrArray as $key => &$value)

	{
		if ($key == "synopsis"){
		  $synopsis_status=true;
		}	

	if (is_array($value) || is_object($value))
		{	
		

			traverse($value);
		}
	else
	{
		  $skipThis = array("Serie A","NFL Network","Various","sport-main","hd","sd","sport-live","notInImdb","portrait","Unknown","featurebox","Visa hela tablån","landscape","sport-schedule-per-day");
		  
		  if (in_array($value, $skipThis)) {

		  }else{	  

  		if ($synopsis_status == true)
		{

			if ($key == "title"){
				$title_status=true;	
				$title=$value;
			
			}

			if ($key == "synopsis"){
			$synopsis = $value;
			$synopsis_status = true;

			}

			if ($key == "streamStart"){
			
			$streamStart = $value;

                        if (holds_int($value)){
                        $streamStart_status = true;
                        }else{
                        continue;
                        }

			}

			if ($key == "streamEnd"){

			$streamEnd = $value;
			$streamEnd_status = true;

			$checkIFok = array($title_status, $synopsis_status, $streamStart_status, $streamEnd_status); 
			$checkIFok_status = false;
			
			global $checkIFok_status;

			foreach ($checkIFok as $value) {
				
			if($value)
			{
				$checkIFok_status = true;
											
			}else{
				$checkIFok_status = false;
			}
			}

			if ($checkIFok_status)
			{
		
			$streamStart = str_replace(':', '', $streamStart);
                        $streamStart = str_replace('-', '', $streamStart);
	        	$streamStart = str_replace('.', '', $streamStart);
			
			$streamEnd = str_replace(':', '', $streamEnd);
			$streamEnd = str_replace('-', '', $streamEnd);
			$streamEnd = str_replace('.', '', $streamEnd);



                        $streamStart =  str_split($streamStart,15)[0];
                        $streamEnd= str_split($streamEnd, 15)[0];

                        $synopsis =  preg_replace( "/\r|\n/", "", $synopsis );
                        $title=  preg_replace( "/\r|\n/", "", $title );


			echo("BEGIN:VEVENT" . "\r\n");
			echo("DTSTART:" . $streamStart . "\r\n" );
			echo("DTEND:" . $streamEnd  . "\r\n");
			echo("DTSTAMP:" . $streamStart . "\r\n");
			echo("CREATED:" . $streamStart . "\r\n");
			echo("DESCRIPTION:" .  $synopsis  . "\r\n");
			echo("LAST-MODIFIED:". $streamStart . "\r\n");
			echo("LOCATION:" . "\r\n");
			echo("SEQUENCE:0" . "\r\n");
			echo("STATUS:CONFIRMED" . "\r\n");
			echo("SUMMARY:" . $title  . "\r\n");
			echo("UID:" . guidv4(random_bytes(16))  . "\r\n");
			echo("TRANSP:OPAQUE" . "\r\n");
			echo("END:VEVENT" . "\r\n");	

			$checkIFok_status = false;
			
			$synopsis=false;
                        $title_status = false;
                        $synopsis_status = false;
                        $streamStart_status = false;
                        $streamEnd_status = false;
							    
			}
										
			}
		}
	}
}
}
}

echo("BEGIN:VCALENDAR"."\r\n");
echo("PRODID:-//Viaplay//Johan_Caripson//FI"."\r\n");
echo("VERSION:2.0"."\r\n");
echo("X-WR-CALNAME:Viaplay Sporti Finland"."\r\n");
echo("X-WR-TIMEZONE:Europe/Helsinki"."\r\n");
echo("X-WR-CALDESC:Viaplay Calender -tiedot osoitteesta www.viaplay.fi"."\r\n");
echo("X-PUBLISHED-TTL:PT15M"."\r\n");
traverse($objOrArray);	
echo("END:VCALENDAR"); 


function guidv4($data)
{
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); 
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); 

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
function holds_int($str)
{
if (preg_match('/[A-Za-z]/', $str) && preg_match('/[0-9]/', $str))
{
                return false;
}
else
{
                return false;
}
}


?>

