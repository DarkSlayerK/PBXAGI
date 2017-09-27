<?php namespace PBX\Process;

use PAGI\Client\Impl\ClientImpl as PagiClient;

class Outbound {

	use \PBX\Traits\Util;

	private $client = null;
	private $cid_name = null;
	private $cid_num = null;
	private $options = "r";
	private $ringtime = 30;
	private $tech = null;

	public function __construct(){
		$this->__log(__FILE__,__LINE__,"Init");
		$this->client = PagiClient::getInstance([]);
	}

	public function setCallerId($callerid){
		$this->cid_num = $callerid;
		return $this;
	}

	public function setCalleridName($name){
		$this->cid_name = $name;
		return $this;
	}

	public function setTech($tech){
		$this->tech = $tech;
		return $this;
	}

	public function setTrunk($trunk){
		$this->trunk = $trunk;
		return $this;
	}

	public function setRingTime($time){
		$this->ringtime = $time;
		return $this;
	}

	public function call($called){
		$this->__log(__FILE__,__LINE__,"Calling " . $called);
		$this->client->setCallerId($this->cid_name, $this->cid_num);
		return $this->client->dial($this->tech . "/" . $called . "@" . $this->trunk, [$this->ringtime, $this->options]);
	}
}
