<?php namespace Bonsum\Services;

use App;
use Bonsum\MongoDB\Donation as DonationModel;
use Bonsum\Services\Localization;


class DonationOption {

	public function __construct(Localization $localization) {

		$this->localization = $localization;
	}

	/**
	 * Retrieve donations options based on a filter array
	 * @param  array   $filter  [description]
	 * @param  integer $index   [description]
	 * @param  integer $count   [description]
	 * @param  [type]  &$total  [description]
	 * @param  boolean $visible [description]
	 * @return [type]           [description]
	 */
	public function retrieveOptions(array $filter, $index = 0, $count = 0, &$total, $visible = true) {

		$options = DonationModel::where('locale', '=', App::getLocale())
		->where('visible', '=', $visible);

		$searchString = array_get($filter, 'searchString');
		if (!is_null($searchString) && $searchString !== '') {
			$options->whereRaw(['$text' => ['$search' => $searchString, '$language' => $this->localization->getLang()]]);
		}

		$total = $options->count();

		$sorting = (array_get($filter, 'sorting') ?: 'popularity');
		$sorting_dir = (array_get($filter, 'sortingDir') ?: 'desc');

		return $options->orderBy($sorting, $sorting_dir)
		->skip($index)
		->take($count)->get();
	}


	/**
	 * Creates a new or updates an existing donation option
	 * @param  array  $fields    [description]
	 * @param  [type] $mongo_id [description]
	 * @return [type]           [description]
	 */
	public function save(array $fields, $mongo_id) {

		$fields['locale'] = App::getLocale();
		$fields['language'] = $this->localization->getLang();
		if (!isset($fields['visible'])) {
			$fields['visible'] = false;
		}

		if (!$mongo_id) {
			DonationModel::create($fields);
		} else {
			$donation = DonationModel::findOrFail($mongo_id);
			$donation->fill($fields);
			$donation->save();
		}
	}

	/**
	 * Set the visibility of a donation option
	 * @param [type] $visibility [description]
	 * @param [type] $mongo_id   [description]
	 */
	public function setVisibility($visibility, $mongo_id) {

		$donation = DonationModel::findOrFail($mongo_id);
		$donation->visible = $visibility;
		$donation->save();
	}

	/**
	 * Destroy a donation option
	 * @param  [type] $mongo_id [description]
	 * @return [type]           [description]
	 */
	public function destroy($mongo_id) {

		DonationModel::destroy($mongo_id);
	}
}
