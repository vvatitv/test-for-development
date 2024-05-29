<?php

namespace App\Libraries\GeoIP;

use Exception;
use Torann\GeoIP\Support\HttpClient;
use Torann\GeoIP\Services\AbstractService;
use Illuminate\Support\Facades\Storage;

class IPInfo extends AbstractService
{
	
	protected $client;

	public function boot()
	{
		$this->client = new HttpClient([
			'base_uri' => ( $this->config('secure') ? 'https' : 'http' ) . '://www.ipinfo.io/',
			'query' => [
				'token' => $this->config('key'),
			],
        ]);
	}

	public function locate($ip)
	{
		$data = $this->client->get($ip);

		if ($this->client->getErrors() !== null)
		{
			throw new Exception('Request failed (' . $this->client->getErrors() . ')');
		}
		$json = json_decode($data[0]);
		return $this->hydrate([
			'ip' => $ip,
			'org' => $json->org,
			'hostname' => $json->hostname,
			'region' => $json->region,
			'timezone' => $json->timezone,
			'lat' => explode(',', $json->loc)[0],
			'lon' => explode(',', $json->loc)[1],
			'city' => $json->city,
			'country' => $this->country(( !empty($json->iso_code) ? $json->iso_code : $json->country )),
			'postal' => $json->postal,
			'iso_code' => ( !empty($json->iso_code) ? $json->iso_code : $json->country ),
		]);
	}

    public function country($code)
    {
        $json_file_path = $this->config('country_path');

        if( is_null($json_file_path) || !file_exists($json_file_path) )
        {
            return $code;
        }
        $fullnames = (array) json_decode(file_get_contents($json_file_path));
        return $fullnames[$code] ?? $code;
    }
}