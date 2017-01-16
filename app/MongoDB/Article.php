<?php namespace Bonsum\MongoDB;

use Jenssegers\Mongodb\Model;
use Carbon\Carbon;
use Bonsum\Helpers\Resource;
use Bonsum\Helpers\Url;
use Route;

class Article extends Model {

	/**
	 * Table name
	 * @var string
	 */
	protected $collection = 'articles';

	protected $connection = 'mongodb';

	protected $guarded = ['id'];

    protected $appends = ['localized_date', 'image_url', 'thumbnail_url', 'date_string', 'edit_link'];

    protected $dates = ['date'];


    public function getTitleTagAttribute() {
        if (!empty($this->attributes['title_tag'])) {
            return $this->attributes['title_tag'];
        }
        return $this->title . ' | ' . trans('general.brand');
    }

    public function getMetaDescriptionAttribute() {
        if (!empty($this->attributes['meta_description'])) {
            return $this->attributes['meta_description'];
        }
        return substr($this->description, 0, 156);
    }

    public function getUrlFriendlyTitleAttribute() {

        if (!empty($this->attributes['url_friendly_title'])) {
            return $this->attributes['url_friendly_title'];
        }
        return Url::makeUrlFriendlyString($this->title);
    }

    public function getLocalizedDateAttribute() {

        return with(new \Jenssegers\Date\Date($this->date))->toFormattedDateString();
    }

    public function getThumbnailUrlAttribute() {

        return Resource::getMediaURL($this->thumbnail);
    }

    public function getImageUrlAttribute() {

        return Resource::getMediaURL($this->image);
    }

    public function getDateStringAttribute() {

        return $this->date->toDateString();
    }

    public function getEditLinkAttribute() {

        if (Route::has('article.edit')) {
            return action('ArticleController@edit', [ $this->id, 'visible' => $this->attributes['visible'] ]);
        }
        return NULL;
    }


    public function toArray($keep_body = false) {

    	$array = parent::toArray();
    	if (!$keep_body) {
    		unset($array['body']);
    	}
    	return $array;
    }

}
