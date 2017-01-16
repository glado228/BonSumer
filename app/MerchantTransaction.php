<?php namespace Bonsum;

use Carbon\Carbon;
use Bonsum\MongoDB\Shop;
use Jenssegers\Eloquent\Model;

class MerchantTransaction extends Model {

	const STATUS_NONE = 0;
	const STATUS_OPEN = 1;
	const STATUS_CONFIRMED = 2;
	const STATUS_CANCELED = 3;

	protected $guarded = ['id'];

	protected $dates = ['clickdate'];

	protected $appends = ['status', 'bonets', 'description', 'localized_amount', 'date'];

	protected $casts = [

		'id' => 'integer',
		'shop_id' => 'integer',
		'user_id' => 'integer',
		'internal_status' => 'integer',
		'status_override' => 'integer',
		'amount' => 'double',
		'commission' => 'double',
		'reward' => 'integer',
		'reward_type' => 'integer'
	];

	/**
	 * return a unique network-specific transaction identifier
	 * @return string combination of network name and network tid
	 */
	public function getNetworkTID() {

		return $this->network . "-" . $this->network_tid;
	}

	/**
	 * Alias of clickdate, added for consistency
	 * @return [type] [description]
	 */
	public function getDateAttribute() {
		return $this->date = $this->clickdate;
	}

	public function getLocalizedAmountAttribute() {

		return app('bonets')->formatCurrency($this->amount, $this->currency);
	}

	public function toArray() {

		$array = parent::toArray();
		$array['clickdate'] = ($this->clickdate instanceof Carbon ? $this->clickdate->toDateString() : $this->clickdate);
		$array['date'] = ($this->date instanceof Carbon ? $this->date->toDateString() : $this->date);
		return $array;
	}

	public static function getValidStates() {
		return [
			self::STATUS_NONE,
			self::STATUS_OPEN,
			self::STATUS_CONFIRMED,
			self::STATUS_CANCELED
		];
	}

	public function getStatusAttribute() {

		switch ($this->internal_status) {

			case self::STATUS_OPEN:
			default:
				return trans('account.bonets_open');
			case self::STATUS_CANCELED:
				return trans('account.bonets_canceled');
			case self::STATUS_CONFIRMED:
				return trans('account.bonets_confirmed');
		}
	}

	public function getBonetsAttribute() {

		if ($this->reward_type === Shop::REWARD_TYPE_PROPORTIONAL) {
			return app('bonets')->convertCommissionToBonets($this->commission, $this->reward);

		} else if ($this->reward_type === Shop::REWARD_TYPE_FIXED) {
			return $this->reward;
		}
		return 0;
	}

	public function getDescriptionAttribute() {

		return $this->program_name;
	}

	public function shop() {

		return $this->belongsTo('Bonsum\MongoDB\Shop', 'shop_id', 'shop_id');
	}

	public function user() {

		return $this->belongsTo('Bonsum\User');
	}
}
