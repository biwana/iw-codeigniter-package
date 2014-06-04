<?php

function flickr_query($api_key, $method, $args = array()) {

#
# build the API URL to call
#

    $settings = array(
        'api_key' => $api_key,
        'method' => $method,
        'format' => 'php_serial',
    );

    $encoded_params = array();
    $params = array_merge($settings, $args);
    
    foreach ($params as $k => $v) {
        $encoded_params[] = urlencode($k) . '=' . urlencode($v);
    }


#
# call the API and decode the response
#

    $url = "https://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
    $rsp = file_get_contents($url);
    $rsp_obj = unserialize($rsp);

    return $rsp_obj;
}