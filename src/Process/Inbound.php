<?php namespace PBX\Process;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use PAGI\Client\Impl\ClientImpl as PagiClient;
use PBX\Models\Number;

class Inbound {

	use \PBX\Traits\Util;

	private $client;
	private $vars;

	public function __construct(){
		$this->client = PagiClient::getInstance([]);
		$this->vars = $this->client->getChannelVariables();
	}

	public function run(){
		try {
			$this->__log(__FILE__, __LINE__, "Init...");

			// Obtenemos el numero en la base de datos
			$number = Number::findByNumber($this->vars->getDNIS());
			
			// Obtenemos el cliente
			$customer = $number->customer;

			// Si el cliente no existe
			if(is_null($customer))
				throw new Exception("Customer not found", 404);

			// Si el cliente esta inactivo
			if($customer->isInactive())
				throw new \Exception("The customer account is disabled", 403);

			// Set userfield
			$this->client->getCDR()->setUserfield($customer->id);

			$this->__log(__FILE__,__LINE__,"userfield setted: " . $customer->id);

			// Obtener destino
			$obj = $this->getDestinyObject($number->destiny_type, $number->destiny_id);

			// Setear customer
			$obj->setCustomer($customer);

			// Setear number
			$obj->setNumber($number);

			// correr...
			$obj->run();

		} catch (ModelNotFoundException $e) {
			$this->__log(__FILE__, __LINE__, $e->getMessage());
		} catch (\Exception $e) {
			$this->__log(__FILE__, __LINE__, $e->getMessage());
		}
	}
}