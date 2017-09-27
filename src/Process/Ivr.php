<?php namespace PBX\Process;

use PAGI\Client\Impl\ClientImpl as PagiClient;
use Illuminate\Database\Capsule\Manager as Capsule;

class IVR {

	use \PBX\Traits\Util;

	private $client = null;
	private $number;
	private $customer;
	private $obj;

	public function __construct($id = null){
		$this->client = PagiClient::getInstance([]);

		if(!is_null($id)){
			$this->__log(__FILE__, __LINE__, 'Get instance');

			$this->obj = Capsule::table('ivr as i')->join('record as r', 'r.id','=','i.record_id')->where('i.id', $id)->first(['i.id','r.location']);

			if(is_null($this->obj))
				throw new \Exception("IVR not found", 404);

			$this->__log(__FILE__, __LINE__, 'Instance getted');
		}
	}

	public function setCustomer(\PBX\Models\Customer $obj){
		$this->customer = $obj;
	}

	public function setNumber(\PBX\Models\Number $obj){
		$this->number = $obj;
	}

	public function run(){

		$this->__log(__FILE__, __LINE__, 'Running...');
		$this->__log(__FILE__, __LINE__, 'Get Options...');

		$options = Capsule::table('ivr_option')->where('ivr_id', $this->obj->id)->get();	
		
		if(count($options)<=0){
			throw new \Exception("Options on IVR not found");
		}

		$this->__log(__FILE__, __LINE__, 'Options getted');

		$audioFile = $this->obj->location;

		if(file_exists($this->obj->location . ".wav"))
			$audioFile = $this->obj->location;
		else
			$audioFile = "/var/lib/asterisk/sounds/default_ivr";

		$this->__log(__FILE__, __LINE__, 'Selected audio: ' . $audioFile);

		$result = $this->client->getData($audioFile, 1500, 5);

		$digits = $result->getDigits();

		$this->__log(__FILE__, __LINE__, "Dialed digits: " . $digits);

		foreach($options as $option){
			if( ($option->option == "t" && empty($digits)) || ($option->option == $digits) ){
				// Get destiny object
				$obj = $this->getDestinyObject($option->destiny_type, $option->destiny_id);
				// Set customer
				$obj->setCustomer($this->customer);
				// Set Number
				$obj->setNumber($this->number);
				// Run
				$obj->run();
				break;
			} else {
				continue;
			}
		}
	}
}