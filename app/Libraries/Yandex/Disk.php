<?php

namespace App\Libraries\Yandex;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class Disk
{
    public function downloadRemoveFile($url, $filename, $path)
    {
        $ch = curl_init($url);
        $save_file_loc = $path . '/' . $filename;

        $fp = fopen($save_file_loc, 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if( file_exists($save_file_loc) )
        {
            return $httpcode;
        }
        
        return false;
    }

    public function getFileInfo($url)
    {
        $ch = curl_init();
        $timeout = 50;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:69.0) Gecko/20100101 Firefox/69.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        $scriptx = '';
        $internalErrors = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML($data);

        foreach($dom->getElementsByTagName('script') as $k => $js)
        {
            if( $js->getAttribute('id') === 'store-prefetch' )
            {
                $scriptx = $js->nodeValue;
            }
        }

        $soxi = json_decode($scriptx, true);

        $sk = $soxi['environment']['sk'];
        $rootsourceId = $soxi['rootResourceId'];
        $hash = urlencode($soxi['resources'][$rootsourceId]['hash']);

        $obj = json_encode([
            'hash' => $hash,
            'sk' => $sk,
        ]);
        
        $download_link = $this->get_datax('https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key=' . $hash);

        $soi = json_decode($download_link, true);

        $returnArray = [
            'name' => $soxi['resources'][$rootsourceId]['name'],
            'download' => $this->get_redifet($soi['href']),
            'data' => $soxi['resources'][$rootsourceId]
        ];

        if( $soxi['resources'][$rootsourceId]['meta']['hasPreview'] )
        {
            $returnArray['preview'] = $soxi['resources'][$rootsourceId]['meta']['xxxlPreview'];
        }

        return $returnArray;
    }

    public function get_redifet($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = curl_exec($ch);
        curl_close($ch);

        if( preg_match('/^Location: (.+)$/im', $headers, $matches) )
        {
            return trim($matches[1]);
        }

        return $url;
    }

    public function get_datax($url)
    {
        $ch = curl_init();
        $timeout = 50;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}