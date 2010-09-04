<?php

$plugin = Inflector::camelize(basename(realpath(__FILE__ . '/../../..')));
foreach (a('geoip', 'geoipregionvars', 'geoipcity') as $filename) {
	App::import('Vendor', $plugin . '.cakephp_maxmind_' . r('/', '_', $filename), aa('file', 'vendors/maxmind/' . $filename . '.php'));
}
App::import('DataSource', $plugin . '.GeoipCommonSource');
unset($plugin);

class MaxmindSource extends GeoipCommonSource {
	
	function selectByIp($config, $ip, $ip_number) {
		if (trim(@$config['path']) == '') return a();
		if (!file_exists(@$config['path'])) return a();

		$gi = geoip_open($config['path'], GEOIP_STANDARD); 
		
		$result = a();
		if ($gi->databaseType == GEOIP_CITY_EDITION_REV1) {
			foreach ((array)geoip_record_by_addr($gi, $ip) as $field => $value) {
				$result[$field] = $value;
			}
		} else {
			$result['country_code'] = geoip_country_code_by_addr($gi, $ip);
			$result['country_name'] = geoip_country_name_by_addr($gi, $ip);
		}
		$result['ip'] = $ip;
		ksort($result);
        geoip_close($gi);

		return $result;
	}
	
}

?>