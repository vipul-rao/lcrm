<?php

namespace App\Helpers;

class Common
{

    public static function parse_template($body)
    {
        if (preg_match_all('/\[(.*?)\]/', $body, $template_vars))
        {
            $replace ='';
            foreach ($template_vars[1] as $var)
            {
                $body = str_replace('[' . $var . ']', $replace, $body);
            }
        }
        return $body;
    }
}