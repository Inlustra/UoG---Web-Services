<?php
Response::macro('xml', function ($keyName, array $vars, $rootElement = 'ArrayOfPost', $prev = true, $status = 200, array $header = [],  $xml = null) {
    if (is_null($xml)) {
        $xml = new SimpleXMLElement('<' . $rootElement . '/>');
    }

    foreach ($vars as $key => $value) {
        if (is_array($value)) {
            if (is_numeric($key)) {
                $prev = false;
                Response::xml($keyName, $value, $rootElement, "", $status, $header, $xml->addChild(str_singular($keyName)));
            } else {
                if ($prev) {
                    Response::xml($keyName, $value, $rootElement, true, $status, $header, $xml->addChild($key));
                } else {
                    Response::xml($key, $value, $rootElement, true, $status, $header, $xml->addChild($key));
                }
            }
        } else {
            if (strpos($key, 'id') !== false) {
                $xml->addAttribute($key, $value);
            } else {
                $xml->addChild($key, $value);
            }
        }
    }

    if (empty($header)) {
        $header['Content-Type'] = 'application/xml';
    }

    return Response::make($xml->asXML(), $status, $header);
});

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 01/02/2015
 * Time: 18:45
 */ 