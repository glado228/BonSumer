<?php namespace Bonsum\MongoDB;

use Jenssegers\Mongodb\Model;
use Bonsum\Helpers\Resource;
use Route;


class Donation extends Model {

	protected $collectiom = 'donations';

	protected $connection = 'mongodb';

	protected $guarded = ['id', '_id'];

	protected $appends = ['thumbnail_url', 'thumbnail_mouseover_url', 'edit_link', 'donation_options'];

	public function getThumbnailUrlAttribute() {
		return Resource::getMediaUrl($this->thumbnail);
	}

	public function getThumbnailMouseoverUrlAttribute() {
		return Resource::getMediaUrl($this->thumbnail_mouseover);
	}

	public function getEditLinkAttribute() {
		if (Route::has('donation.edit')) {
			return action('RedeemController@edit', [ $this->id, 'visible' => $this->attributes['visible'] ]);
		}
		return NULL;
	}

	public function setPopularityAttribute($value) {
		$this->attributes['popularity'] = intval($value);
	}

	public function setDonationSizesAttribute(array $value) {
		$this->attributes['donation_sizes'] = array_flatten($value);
		sort($this->attributes['donation_sizes'], SORT_NUMERIC);
	}

	public function getDonationOptionsAttribute() {

		$conversion = app('bonets');

		$options = [];
		foreach ($this->donation_sizes as $size) {
			$value = $conversion->fromBonets($size);
			$localized_amount = $conversion->formatCurrency($value);
			$options[] = [
					'label' => trans('redeem.donation_label', ['bonets' => $size, 'amount' => $localized_amount]),
					'value' => $size,
					'success_message' => trans('redeem.donation_success_message', ['amount' => $localized_amount, 'bonets' => $size, 'receiver' => $this->name])
			];
		}
		return $options;
	}

	public function bonets_donations() {

		return $this->hasMany('Bonsum\BonetsDonation');
	}
}
