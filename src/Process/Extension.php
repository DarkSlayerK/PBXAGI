<?php namespace PBX\Process;

use PAGI\Client\Impl\ClientImpl as PagiClient;
use PBX\Models\Extension as Exten;

class Extension {

	use \PBX\Traits\Util;

	private $obj;
	private $customer;
	private $number;
	private $client;

	public function __construct($id = null){
		try {
			$this->client  = PagiClient::getInstance([]);
			$this->obj = Exten::findOrFail($id);
		} catch (\Exception $e) {
			$this->__log(__FILE__, __LINE__, "Extension not found");
		}
	}
	
	public function setCustomer(\PBX\Models\Customer $obj){
		$this->customer = $obj;
	}

	public function setNumber(\PBX\Models\Number $obj){
		$this->number = $obj;
	}

	public function run(){


		$this->__log(__FILE__, __LINE__, "Process...");

		$result = $this->sipCall();

		if (!$result->isAnswer()) {
			$this->__log(__FILE__,__LINE__,"SIP " . $this->obj->accountcode . " no esta conectado o esta ocupado");
			$this->__log(__FILE__,__LINE__,"Intentando marcar al IAX2");

			$result = $this->iax2Call();

			if (!$result->isAnswer()) {
				$this->__log(__FILE__,__LINE__,"IAX2 " . $this->obj->accountcode . " no esta conectado o esta ocupado");

				// Salir a Desvio
				$result = (new \PBX\Process\Outbound())->setCallerId( $this->sanitizeCid($this->number->number) )
				->setCallerIdName( $this->number->cid_name )
				->setTech("SIP")
				->setTrunk("pstn")
				->setRingTime($this->obj->redirect_to_ringtime)
				->call( "511" . $this->obj->redirect_to );

				if (!$result->isAnswer()) {
					$this->__log(__FILE__, __LINE__, $this->obj->redirect_to . " no contesto la llamada");
				}
			}
		}
	}

	public function iax2Call($options = "r"){
		return $this->client->dial('IAX2/' . $this->obj->accountcode, [$this->obj->ringtime, $options]);
	}

	public function sipCall($options = "r"){
		return $this->client->dial('SIP/' . $this->obj->accountcode, [$this->obj->ringtime, $options]);
	}

}