<?php namespace Bonsum;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BonetsCredit extends Model {

	protected $guarded = ['id'];

	protected $dates = ['date'];

	protected $appends = ['status'];

	protected $casts = [
		'id' => 'integer',
		'bonets' => 'integer',
		'user_id' => 'integer'
 	];

	public function toArray() {
		$array = parent::toArray();
		$array['date'] = ($this->date instanceof Carbon ? $this->date->toDateString() : $this->date);
		return $array;
	}

	public function user() {

		return $this->belongsTo('Bonsum\User');
	}

	public function getDescriptionAttribute() {

		if ($this->attributes['description']) {
			return $this->attributes['description'];
		}
		return trans('account.bonets_credit');
	}

	public function getStatusAttribute() {

		return trans('account.bonets_confirmed');
	}
}
