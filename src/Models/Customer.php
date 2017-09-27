<?php namespace PBX\Models;

use Illuminate\Database\Eloquent\Model as Model;

class Customer extends Model {

	protected $table = 'customer';

	public function isInactive(){
		return $this->state == 'I';
	}
}
