<?php

class HelperUtil
{

    public static function toEloquent($model, $post)
    {
        $xml = simplexml_load_string($post);
        foreach ($xml as $key => $value) {
            if (!is_array($value)) {
                $string = $value->__toString();
                if(!empty($string)) {
                    $model->$key = $string;
                }
            }
        }
        return $model;
    }
}
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 19/02/2015
 * Time: 22:57
 */ 