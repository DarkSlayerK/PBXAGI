<?php namespace PBX\Traits;

use Carbon\Carbon;
use PAGI\Client\Impl\ClientImpl as PagiClient;

trait Util {

	function getDestinyObject($destiny, $id){
		$class = '\PBX\Process\\' . ucfirst(strtolower($destiny));
		return new $class($id);
	}

	function __log($file, $line, $message){
		$client = PagiClient::getInstance([]);
		$msg = "[" . Carbon::now()->format('Y-m-d H:i:s') . "][" . $file . "(" . $line . ")] " . $message . PHP_EOL;
		//file_put_contents("/var/log/pbx/" . $client->getChannelVariables()->getUniqueId() . ".log", $msg, FILE_APPEND);
		file_put_contents("/var/log/pbx/general.log", $msg, FILE_APPEND);
		$client->getAsteriskLogger()->debug( $msg );
	}

	function sanitizeCid($callerid){
		if(substr($callerid, 0, 2) == "51"){
			return "0".substr($callerid, 2);
		}
	}

}