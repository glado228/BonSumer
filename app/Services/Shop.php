<?php namespace Bonsum\Services;

use Bonsum\MongoDB\Shop as ShopModel;
use App;
use Bonsum\Services\Localization;

class Shop {


	public function __construct(Localization $localization) {

		$this->localization = $localization;
	}

	/**
	 * Retrieve shops based on a filter array
	 * @param  array   $filter  [description]
	 * @param  integer $index   [description]
	 * @param  integer $count   [description]
	 * @param  [type]  &$total  [description]
	 * @param  boolean $visible [description]
	 * @return [type]           [description]
	 */
	public function retrieveShops(array $filter, $index = 0, $count = 0, &$total, $visible = true) {

		$shops = ShopModel::where('locale', '=', App::getLocale())
		->where('visible', '=', $visible);

		$searchString = array_get($filter, 'searchString');
		if (!is_null($searchString) && $searchString !== '') {
           $shops->whereRaw(['$text' => ['$search' => $searchString, '$language' => $this->localization->getLang()]]);
        }

        if (array_get($filter, 'with_vouchers')) {
			$shops->whereRaw(['vouchers.codes.0' => ['$exists' => true]]);
		}

		$criteria = array_get($filter, 'criteria', []);
		if (!is_array($criteria)) {
			$criteria = json_decode($criteria);
		}

		if (is_array($criteria)) {
			foreach ($criteria as $key => $val) {
				if (!empty($val)) {
					$shops->where('shop_criteria.'. intval($key), '=', true);
				}
			}
		}

		$types = array_get($filter, 'types');
		if (!is_array($types)) {
			$types = json_decode($types);
		}

		if (is_array($types)) {
			foreach ($types as $key => $val) {
				if (!empty($val)) {
					$shops->orWhere('shop_type.'. intval($key), '=', true);
				}
			}
		}

		$total = $shops->count();

		$sorting = (array_get($filter, 'sorting') ?: 'popularity');
		$sorting_dir = (array_get($filter, 'sortingDir') ?: 'desc');

		return $shops->project(['vouchers.codes' => ['$slice' => 1]])
		->orderBy($sorting, $sorting_dir)
		->skip($index)
		->take($count)->get();
	}

	/**
	 * Create a new shop or apdates an existing one
	 * @param  array  $fields   [description]
	 * @param  [type] $mongo_id [description]
	 * @return [type]           [description]
	 */
	public function save(array $fields, $mongo_id) {

		$fields['shop_id'] = (!empty($fields['shop_id']) ? $fields['shop_id'] : ShopModel::max('shop_id')+1);
		if (!empty($fields['tags'])) {
			$fields['tags'] = parse_tags($fields['tags']);
		}
		$fields['locale'] = App::getLocale();
		$fields['language'] = $this->localization->getLang();
		if (!isset($fields['visible'])) {
			$fields['visible'] = false;
		}

		if (!$mongo_id) {
			ShopModel::create($fields);
		} else {
			$shop = ShopModel::findOrFail($mongo_id);
			$shop->fill($fields);
			$shop->save();
		}
	}

	/**
	 * Deletes a shop
	 * @param  [type] $mongo_id [description]
	 * @return [type]           [description]
	 */
	public function destroy($mongo_id) {
		ShopModel::destroy($mongo_id);
	}

	/**
	 * Add vouchers to a shop
	 * @param array  $codes    [description]
	 * @param [type] $value    [description]
	 * @param [type] $mongo_id [description]
	 */
	public function addVouchers(array $codes, $value, $mongo_id) {

		$trimmed_codes = array_map('trim', $codes);

		$codes = array_filter($trimmed_codes, function($code) {
			return $code != '';
		});

		$shop = ShopModel::findOrFail($mongo_id);
		$shop->addVouchers($codes, $value);
	}

	/**
	 * Deletes a voucher from a shop
	 * @param  [type] $code     [description]
	 * @param  [type] $mongo_id [description]
	 * @return [type]           [description]
	 */
	public function deleteVoucher($code, $mongo_id) {

		$shop = ShopModel::findOrFail($mongo_id);
		$shop->deleteVoucher($code);
	}


	/**
	 * set the visibility of a shop
	 * @param boolean $visibility [description]
	 * @param [type] $mongo_id   [description]
	 */
	public function setVisibility($visibility, $mongo_id) {

		$shop = ShopModel::findOrFail($mongo_id);
		$shop->visible = $visibility;
		$shop->save();
	}

	/**
	 * Given an amount and a shop id, atomically removes the first voucher code corresponding
	 * to that amount
	 * @param  int $amount   the desired vouhcer's value in the shop's currency
	 * @param  string      the shop's mongo id
	 * @return array       (voucher_code, shop)    the voucher code or null of none was found, and the shop
	 */
	public function getVoucher($amount, $mongo_id) {

		$voucher_code = NULL;

		/*
			Atomically remove the first voucher for the amount
		 */

		$shop = ShopModel::where('_id', '=', $mongo_id)->where('visible', '=', true)->project(['vouchers.codes' => ['$slice' => 1]])->first();
		if (!$shop) {
			throw new \Exception('could not retrieve voucher for shop ' . $mongo_id . ' with amount ' . $amount . '. Shop does not exist');
		}

		// retrieve index for this voucher amount
		$voucher_list_index = $shop->getIndexForVoucherValue($amount);

		if ($voucher_list_index !== NULL) {

			$shop = ShopModel::raw(function($collection) use ($mongo_id, $amount, $voucher_list_index) {

				return $collection->findAndModify(
				[
					'_id' => new \MongoId($mongo_id),
					'vouchers.'.$voucher_list_index.'.codes' => [
						'$not'=> ['$size' => 0]
					]
				],
				[
					'$pop' => [
						'vouchers.'.$voucher_list_index.'.codes' => -1
					]
				],
				[
					'vouchers.codes' => [
						'$slice' => 1
					]
				]
				);
			});

			if ($shop) {
				$vouchers = $shop->vouchers;
				if (is_array($vouchers) && !empty($vouchers[$voucher_list_index])) {
					$codes = $vouchers[$voucher_list_index]['codes'];
					$voucher_code = $codes[0];
				}
			}
		}

		return [$voucher_code, $shop];
	}
}
