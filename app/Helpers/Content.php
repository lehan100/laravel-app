<?php

namespace App\Helpers;

class Content
{

    public static function minifyContent($content)
    {
        $replace = array(
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/" => '<?php ',
            "/\n([\S])/" => '$1',
            "/\r/" => '',
            "/\n/" => '',
            "/\t/" => '',
            "/ +/" => ' ',
        );
        $buffer = preg_replace(array_keys($replace), array_values($replace), $content);
        return $buffer;
    }

    public static function miniString($string = "", $word = 25, $end = "...")
    {
        $string = strip_tags($string);
        $string = str_replace('&nbsp;', ' ', $string);

        return \Illuminate\Support\Str::words($string, $word, $end);
    }
}
