<?php

class CallMe {

	protected $skypeStatus = 'Offline';
	protected $skypeUrl = 'http://mystatus.skype.com/$0.txt';
	protected $latitudeLocation = '';
	protected $latitudeUrl = 'http://www.google.com/latitude/apps/badge/api?user=$0&type=json';
	protected $hostipUrl = 'http://api.hostip.info/country.php?ip=';
	protected $userCountry = '';
	protected $isMobile = false;
	protected $callProto = 'callto:';
	protected $numberCountry = '';

	public function __construct($options) {
			$this->options = $options;
			$this->_getSkypeStatus();
			$this->_getLatitude();
			$this->_getUserLocation();
			$this->_checkMobile();
	}

	private function _getAllNumbers() {
			if (!is_array($this->options['numbers'])) {
					$this->options['numbers'] = array();
			}
			if ($this->latitudeLocation) {
					if (isset($this->options['numbers'][$this->latitudeLocation])) {
						$country = $this->options['numbers'][$this->latitudeLocation];
					} else {
						$country = array_shift($this->options['numbers']);
					}
			} else {
					$country = array_shift($this->options['numbers']);
			}
			return $country;
	}

	private function _getNumber($type = null) {
			$numbers = $this->_getAllNumbers();
			if ($type != null) {
				return $numbers[$type];
			}
			return array_shift($numbers);
	}

	public function getCallTypes() {
		$buttons = array();
		if ($this->isMobile == true) {
			foreach ($this->_getAllNumbers() as $k => $num) {
				$buttons[] = array(
					'type' => $k,
					'href' => $this->callProto . $num
				);
			}
			$buttons[] = array(
				'type' => 'text',
				'href' => 'sms:' . $this->_getNumber('cell')
			);
		}
		foreach ($this->_getAllNumbers() as $k => $num) {
			$buttons[] = array(
				'type' => 'skypephone',
				'href' => $this->callProto . $num
			);
		}
		if ($this->skypeStatus == 'Online' /*&& !$this->isMobile*/) {
			$buttons[] = array(
				'type' => 'skypecall',
				'href' => 'skype:'. $this->options['skype']
			);
		}
		return $buttons;
	}

	private function _checkMobile() {
		$detect = new Mobile_Detect();
		if ($detect->isMobile()) {
			$this->isMobile = true;
			$this->callProto = 'tel:';
		}
	}

	private function _getUserLocation() {
		try {
			// nginx forwarded IP check
			$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			$url = $this->hostipUrl . $ip;
			$country = file_get_contents($url);
			if ($country != 'XX') {
				$this->userCountry = $country;
			}
		} catch (Exception $e) {}
	}

	private function _getLatitude() {
			try {
				$url = str_replace('$0', $this->options['latitude'], $this->latitudeUrl);
				$strJSON = file_get_contents($url);
				$json = json_decode($strJSON, true);
				if ($json) {
					$location = $json['features'][0]['properties']['reverseGeocode'];
					$country = array_pop(explode(' ',$location));
					$this->latitudeLocation = $country;
					if (isset($this->options['numbers'][$country])) {
						$this->numberCountry = $country;
					} else {
						$this->numberCountry = array_shift(array_keys($this->options['numbers']));
					}
				}
			} catch (Exception $e) {}
	}

	private function _getSkypeStatus() {
			try {
				$url = str_replace('$0', $this->options['skype'], $this->skypeUrl);
				$status = file_get_contents($url);
				if ($status == 'Online') {
					$this->skypeStatus = $status;
				}
			} catch (Exception $e) {}
	}

	public function getLocation() {
		return $this->numberCountry;
	}

}
