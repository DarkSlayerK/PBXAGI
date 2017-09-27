<?php namespace PBX\Models;

use Illuminate\Database\Eloquent\Model as Model;

class Number extends Model {

	protected $table = 'number';

	public static function findByNumber($number){
		return self::where('number', $number)->firstOrFail();
	}

	public function customer(){
		return $this->belongsTo('PBX\Models\Customer', 'customer_id', 'id');
	}
}
