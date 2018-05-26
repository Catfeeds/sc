<?php

namespace App;

class StringToolkit
{
    public static function template($string, array $variables)
    {
        if (empty($variables)) {
            return $string;
        }

        $search = array_keys($variables);
        array_walk($search, function (&$item) {
            $item = '{{' . $item . '}}';
        });

        $replace = array_values($variables);

        return str_replace($search, $replace, $string);
    }

    public static function sign($data, $key)
    {
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data);

        return md5(json_encode($data) . $key);
    }

    public static function secondsToText($value)
    {
        $minutes = intval($value / 60);
        $seconds = $value - $minutes * 60;
        return sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
    }

    public static function textToSeconds($text)
    {
        if (strpos($text, ':') === false) {
            return 0;
        }
        list($minutes, $seconds) = explode(':', $text, 2);
        return intval($minutes) * 60 + intval($seconds);
    }

    public static function plain($text, $length = 0)
    {
        $text = strip_tags($text);

        $text = str_replace(array("\n", "\r", "\t"), '', $text);
        $text = str_replace('&nbsp;', ' ', $text);
        $text = trim($text);

        $length = (int)$length;
        if (($length > 0) && (mb_strlen($text) > $length)) {
            $text = mb_substr($text, 0, $length, 'UTF-8');
            $text .= '...';
        }

        return $text;
    }

    public static function createRandomString($length, $lowerCase = false)
    {
        if ($lowerCase) {
            $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        } else {
            $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }

        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function printMem($bytes)
    {
        $format = function ($number) {
            return number_format($number, 2);
        };
        if ($bytes < 1024 * 1024 * 1024) {
            return call_user_func($format, $bytes / 1024 / 1024) . "M";
        } else {
            return call_user_func($format, $bytes / 1024 / 1024 / 1024) . "G";
        }
    }

    public static function emojiEncode($str)
    {
        if (!is_string($str)) return $str;
        if (!$str || $str == 'undefined') return '';

        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
            return addslashes($str[0]);
        }, $text); //将emoji的unicode留下，其他不动
        return json_decode($text);
    }

    public static function emojiDecode($str)
    {
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, $text); //将两条斜杠变成一条，其他不动
        return json_decode($text);
    }

    public static function recalculateSerialized($serialized)
    {
        return preg_replace_callback('#s:(\d+):"(.*?)";#s', function ($match) {
            return 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $serialized);
    }

//    public static function purifyHtml($html, $trusted = false)
//    {
//        if (empty($html)) {
//            return '';
//        }
//
//        $config = array(
//            'cacheDir' => ServiceKernel::instance()->getParameter('kernel.cache_dir') .  '/htmlpurifier'
//        );
//
//        $factory = new HTMLPurifierFactory($config);
//        $purifier = $factory->create($trusted);
//
//        return $purifier->purify($html);
//    }

    public static function replacePunctuation($text, $replace = '')
    {
        $char = " 。，、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐¸﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";

        $pattern = array(
            "/[[:punct:]]/i", //英文标点符号
            '/[' . $char . ']/u', //中文标点符号
            '/[ ]{2,}/'
        );
        return preg_replace($pattern, $replace, $text);
    }

}