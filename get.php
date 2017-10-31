<?php

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://api.vk.com/method/wall.get?domain=jonkofee_music&filter=owner&access_token=630494155c417d7382ffe1fc428faa0e344b3763a97f921694f65b91c1e4468ff261900562c8bf4dfbb32&v=5.69",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"cache-control: no-cache"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo $response;
}