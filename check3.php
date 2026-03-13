<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/api/products?limit=2");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
echo "STATUS: " . $info['http_code'] . "\n";
echo substr($output, 0, 500);
