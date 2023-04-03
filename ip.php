<?PHP

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}


$user_ip = getUserIP();

//echo $user_ip; // Output IP address [Ex: 177.87.193.134]
$ip =  $user_ip;
$ip_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));  

if($ip_info && $ip_info->geoplugin_countryName != null){
	echo 'IP = '.$ip.'<br/>';
	echo 'Country = '.$ip_info->geoplugin_countryName.'<br/>';
	echo 'Country Code = '.$ip_info->geoplugin_countryCode.'<br/>';
	echo 'City = '.$ip_info->geoplugin_city.'<br/>';
	echo 'Region = '.$ip_info->geoplugin_region.'<br/>';
	echo 'Latitude = '.$ip_info->geoplugin_latitude.'<br/>';
	echo 'Longitude = '.$ip_info->geoplugin_longitude.'<br/>';
	echo 'Timezone = '.$ip_info->geoplugin_timezone.'<br/>';
	echo 'Continent Code = '.$ip_info->geoplugin_continentCode.'<br/>';
	echo 'Continent Name = '.$ip_info->geoplugin_continentName.'<br/>';
	echo 'Timezone = '.$ip_info->geoplugin_timezone.'<br/>';
	echo 'Currency Code = '.$ip_info->geoplugin_currencyCode;
}

function get_browser_name($user_agent){
    $t = strtolower($user_agent);
    $t = " " . $t;
    if     (strpos($t, 'opera'     ) || strpos($t, 'opr/')     ) return 'Opera'            ;   
    elseif (strpos($t, 'edge'      )                           ) return 'Edge'             ;   
    elseif (strpos($t, 'chrome'    )                           ) return 'Chrome'           ;   
    elseif (strpos($t, 'safari'    )                           ) return 'Safari'           ;   
    elseif (strpos($t, 'firefox'   )                           ) return 'Firefox'          ;   
    elseif (strpos($t, 'msie'      ) || strpos($t, 'trident/7')) return 'Internet Explorer';
    return 'Unkown';
}
echo "<br/> Browser:";
echo get_browser_name($_SERVER['HTTP_USER_AGENT']);//Chrome

?>