<?php

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://volleyballapi.p.rapidapi.com/api/volleyball/match/oCpswQIb/h2h",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"x-rapidapi-host: volleyballapi.p.rapidapi.com",
		"x-rapidapi-key: db002aa75cmsh6f98a11a7815cbep113ce3jsn4825c53f0846"
	],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
    
	echo $response;
}

?>