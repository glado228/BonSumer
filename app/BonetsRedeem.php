<?php namespace Bonsum;

use Jenssegers\Eloquent\Model;
use Carbon\Carbon;

class BonetsRedeem extends Model {

	protected $guarded = ['id'];

	protected $dates = ['date'];

	protected $appends = ['localized_amount', 'description'];

	protected $casts = [
		'id' => 'integer',
		'bonets' => 'integer',
		'user_id' => 'integer',
		'amount' => 'double'
 	];

	public function toArray() {

		$array = parent::toArray();
		$array['date'] = ($this->date instanceof Carbon ? $this->date->toDateString() : $this->date);
		return $array;
	}

	public function getDescriptionAttribute() {

		if ($this->shop) {
			return $this->shop->name;
		}
		return '';
	}

	public function getLocalizedAmountAttribute() {

		return app('bonets')->formatCurrency($this->amount, $this->currency);
	}


	public function user() {

		return $this->belongsTo('Bonsum\User');
	}

	public function shop() {

		return $this->belongsTo('Bonsum\MongoDB\Shop');
	}

}
