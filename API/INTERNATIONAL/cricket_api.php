<?php

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://cricbuzz-cricket.p.rapidapi.com/matches/v1/recent",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"x-rapidapi-host: cricbuzz-cricket.p.rapidapi.com",
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