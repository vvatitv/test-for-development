<?php

namespace App\Libraries\GeoIP;

use Exception;
use Torann\GeoIP\Support\HttpClient;
use Torann\GeoIP\Services\AbstractService;

class Geojs extends AbstractService
{
	
	protected $client;

	public function boot()
	{
		$this->client = new HttpClient();
	}

	public function locate($ip)
	{
		$data = $this->client->get(($this->config('secure') ? 'https' : 'http') . '://get.geojs.io/v1/ip/geo/' . $ip . '.json');

		if ($this->client->getErrors() !== null)
		{
			throw new Exception('Request failed (' . $this->client->getErrors() . ')');
		}

		$json = json_decode($data[0]);

		return $this->hydrate([
			'ip' => $ip,
			'organization_name' => $json->organization_name,
			'region' => $json->region,
			'accuracy' => $json->accuracy,
			'asn' => $json->asn,
			'organization' => $json->organization,
			'timezone' => $json->timezone,
			'lat' => $json->latitude,
			'lon' => $json->longitude,
			'country_code3' => $json->country_code3,
			'area_code' => $json->area_code,
			'city' => $json->city,
			'country' => $json->country,
			'country_code' => $json->country_code,
			'continent' => $json->continent_code,
			'iso_code' => ( !empty($json->iso_code) ? $json->iso_code : $json->country_code ),
		]);
	}
}