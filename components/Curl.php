<?php

namespace components;

class Curl
{
    public static function execute($url)
    {
        $curl = self::prepare($url);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public static function executeMultiple($urls)
    {
        $curl_handle_arr = [];
        $result_arr = [];

        $master = curl_multi_init();

        foreach ($urls as $key => $url) {
            $curl_handle = self::prepare($url);
            $curl_handle_arr[$key] = $curl_handle;
            curl_multi_add_handle($master, $curl_handle);
        }

        do {
            curl_multi_exec($master, $running);
            curl_multi_select($master);
        } while ($running > 0);

        foreach ($urls as $key => $url) {
            $curl_handle = $curl_handle_arr[$key];
            $result_arr[$key] = curl_multi_getcontent($curl_handle);
            curl_multi_remove_handle($master, $curl_handle);
        }

        curl_multi_close($master);

        return $result_arr;
    }

    private static function prepare($href)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $href);
        curl_setopt($curl, CURLOPT_REFERER, $href);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
        return $curl;
    }
}