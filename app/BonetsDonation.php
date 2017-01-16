<?php namespace Bonsum;

use Jenssegers\Eloquent\Model;
use Carbon\Carbon;

class BonetsDonation extends Model {

	protected $guarded = ['id'];

	protected $dates = ['date'];

	protected $appends = ['receiver', 'localized_amount'];

	protected $hidden = ['donation'];

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

	public function getLocalizedAmountAttribute() {

		return app('bonets')->formatCurrency($this->amount, $this->currency);
	}

	public function getReceiverAttribute() {

		if ($this->donation) {
			return $this->donation->name;
		}
		return '';
	}

	public function user() {

		return $this->belongsTo('Bonsum\User');
	}

	public function donation() {

		return $this->belongsTo('Bonsum\MongoDB\Donation');
	}

}
