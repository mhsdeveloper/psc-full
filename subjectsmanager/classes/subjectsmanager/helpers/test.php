<?php

function getAPI($url){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $resp = curl_exec($curl);
    curl_close($curl);

    echo $resp;
    $resp = json_decode($resp, true);
    print_r($resp[0]["id"]);
}


getAPI("http://192.168.56.57/subjectsmanager/gettopicid?topic=Anti-Slavery%20Movements");