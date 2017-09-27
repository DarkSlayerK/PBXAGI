<?php namespace PBX\Process;

use PAGI\Client\Impl\ClientImpl as PagiClient;

class Operator {

	use \PBX\Traits\Util;

	private $client = null;
	private $number = null;
	private $customer = null;

	public function __construct(){
		$this->client = PagiClient::getInstance([]);
	}

	public function setCustomer(\PBX\Models\Customer $obj){
		$this->customer = $obj;
	}

	public function setNumber(\PBX\Models\Number $obj){
		$this->number = $obj;
	}

	public function run(){

		if(!is_null($this->number))
			$this->client->setCallerId($this->number->cid_name, $this->sanitizeCid($this->number->number));

		$this->client->dial('SIP/123456@192.168.50.201:5160', [60,'tTw']);
	}
}
