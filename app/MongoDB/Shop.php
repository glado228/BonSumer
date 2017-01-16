<?php namespace Bonsum\MongoDB;

use Jenssegers\Mongodb\Model;
use Bonsum\Helpers\Resource;
use Route;


class Shop extends Model {

	protected $collectiom = 'shops';

	protected $connection = 'mongodb';

	protected $guarded = ['id', '_id'];

	protected $appends = ['bonets_per', 'thumbnail_url', 'thumbnail_mouseover_url', 'edit_link', 'voucher_options'];

	public static $affiliates = [
		\Bonsum\Affiliate\Adcell::NETWORK_NAME,
		\Bonsum\Affiliate\Affilinet::NETWORK_NAME_1,
		\Bonsum\Affiliate\Affilinet::NETWORK_NAME_2,
		\Bonsum\Affiliate\Belboon::NETWORK_NAME,
		\Bonsum\Affiliate\CJ::NETWORK_NAME,
		\Bonsum\Affiliate\Tradedoubler::NETWORK_NAME,
		\Bonsum\Affiliate\Webgains::NETWORK_NAME,
		\Bonsum\Affiliate\Zanox::NETWORK_NAME
	];

	const SHOP_TYPE_FINANCE = 0;
	const SHOP_TYPE_TECHNOLOGY = 1;
	const SHOP_TYPE_LIFESTYLE = 2;
	const SHOP_TYPE_PETS = 3;
	const SHOP_TYPE_HOME = 4;
	const SHOP_TYPE_BABY = 5;
	const SHOP_TYPE_BEAUTY = 6;
	const SHOP_TYPE_FOOD = 7;
	const SHOP_TYPE_FASHION = 8;

	public static $shopTypes = [
		self::SHOP_TYPE_FINANCE => 'finance',
		self::SHOP_TYPE_TECHNOLOGY => 'technology',
		self::SHOP_TYPE_LIFESTYLE => 'lifestyle',
		self::SHOP_TYPE_PETS => 'pets',
		self::SHOP_TYPE_HOME => 'home',
		self::SHOP_TYPE_BABY => 'baby',
		self::SHOP_TYPE_BEAUTY => 'beauty',
		self::SHOP_TYPE_FOOD => 'food',
		self::SHOP_TYPE_FASHION => 'fashion'
	];


	const SHOP_CRITERION_ETHICALLY_MANUFACTURED = 0;
	const SHOP_CRITERION_NO_ANIMAL_EXPERIMENTS = 1;
	const SHOP_RESOURCE_EFFICIENT = 2;
	const SHOP_HEALTHY_MATERIALS = 3;
	const SHOP_LOW_CO2_FOOTPRINT = 4;

	public static $shopCriteria = [
		self::SHOP_CRITERION_ETHICALLY_MANUFACTURED => 'ethically_manufactured',
		self::SHOP_CRITERION_NO_ANIMAL_EXPERIMENTS => 'no_animal_experiments',
		self::SHOP_RESOURCE_EFFICIENT => 'resource_efficient',
		self::SHOP_HEALTHY_MATERIALS => 'healthy_materials',
		self::SHOP_LOW_CO2_FOOTPRINT => 'low_co2_footprint'
	];

	const REWARD_TYPE_NO_REWARD = 0;
	const REWARD_TYPE_PROPORTIONAL = 1;
	const REWARD_TYPE_FIXED = 2;


	public function getBonetsPerAttribute() {
		if ($this->reward_type === self::REWARD_TYPE_PROPORTIONAL) {
			return trans('shop.bonets_per_currency_unit', ['bonets' => $this->proportional_reward, 'curr' => app('bonets')->formatCurrencySymbol()]);
		} else if ($this->reward_type === self::REWARD_TYPE_FIXED) {
			return trans('shop.bonets_per_purchase', ['bonets' => $this->fixed_reward]);
		}
		return NULL;
	}

	public function getThumbnailUrlAttribute() {
		return Resource::getMediaUrl($this->thumbnail);
	}

	public function getThumbnailMouseoverUrlAttribute() {
		return Resource::getMediaUrl($this->thumbnail_mouseover);
	}

	public function getEditLinkAttribute() {

		if (Route::has('shop.edit')) {
			return action('ShopController@edit', [ $this->id, 'visible' => $this->attributes['visible'] ]);
		}
		return NULL;
	}

	public function deleteVoucher($code) {

		$code = trim($code);

		foreach ($this->vouchers as $index => $v_entry) {
			$this->pull('vouchers.'.$index.'.codes', $code);
		}
	}

	public function getIndexForVoucherValue($value) {

		if (is_array($this->vouchers)) {
			foreach ($this->vouchers as $index => $v_entry) {
				if ($v_entry['value'] === $value) {
					return $index;
				}
			}
		}
		return NULL;
	}

	public function addVouchers(array $codes, $value) {

		$value = intval($value);

		$index = $this->getIndexForVoucherValue($value);
		if (!is_null($index)) {
			$this->push('vouchers.'.$index.'.codes', $codes);
		} else {
			$this->push('vouchers', ['value' => $value, 'codes' => $codes]);
		}
	}

	public function setPopularityAttribute($value) {
		$this->attributes['popularity'] = intval($value);
	}

	public function setShopTypeAttribute(array &$shop_type) {

		$new_types = [];
		foreach (array_keys(self::$shopTypes) as $type) {
			$new_types[] = !empty($shop_type[$type]);
		}
		$this->attributes['shop_type'] = $new_types;
	}

	public function setShopCriteriaAttribute(array &$shop_criteria) {
		foreach (array_keys(self::$shopCriteria) as $cr) {
			$this->attributes['shop_criteria'][$cr] = !empty($shop_criteria[$cr]);
		}
	}

	public function hasCriterion($criterion) {

		return !empty($this->shop_criteria[$criterion]);
	}

	public function setRewardTypeAttribute($reward_type) {
		$this->attributes['reward_type'] = intval($reward_type);
	}

	public function setProportionalRewardAttribute($value) {
		$this->attributes['proportional_reward'] = intval($value);
	}

	public function setFixedRewardAttribute($value) {
		$this->attributes['fixed_reward'] = intval($value);
	}

	public function setShopIdAttribute($shop_id) {
		// we want MongoDB to always store this as an integer
		$this->attributes['shop_id'] = intval($shop_id);
	}

	public function getVoucherOptionsAttribute() {

		$conversion = app('bonets');

		$options = [];
		if (is_array($this->vouchers)) {
			foreach ($this->vouchers as $index => $v_entry) {
				$value = $v_entry['value'];
				$codes = $v_entry['codes'];
				if (count($codes) > 0) {
					$bonets_value = $conversion->toBonets($value);
					$localized_amount = $conversion->formatCurrency($value);
					$options[] = [
						'label' => trans('redeem.voucher_label', ['bonets' => $bonets_value, 'amount' => $localized_amount]),
						'bonets_value' => $bonets_value,
						'value' => $value,
						'success_message' => trans('redeem.voucher_success_message', ['amount' => $localized_amount, 'shop' => $this->name])
					];
				}
			}
		}
		return $options;
	}

	public function toArray($hideVouchers = true, $tags_as_comma_separated_list = false) {

		$array = parent::toArray();

		if ($hideVouchers) {
			unset($array['vouchers']);
		}

		if ($tags_as_comma_separated_list) {
			$list = '';
			if (is_array($this->tags)) {
				foreach ($this->tags as $tag) {
					$list .= (($list !== '') ? ', ' : '') . $tag['text'];
				}
			}
			$array['tags'] = $list;
		}

		return $array;
	}

	public function merchant_transactions() {

		return $this->hasMany('Bonsum\MerchantTransaction', 'shop_id', 'shop_id');
	}

	public function bonets_redeems() {

		return $this->hasMany('Bonsum\BonetsRedeem');
	}
}
