<?php namespace Bonsum\Services;

use Bonsum\MongoDB\Article as ArticleModel;
use Bonsum\Helpers\Url;
use Carbon\Carbon;
use App;
use Bonsum\Services\Localization;

class Article {

	public function __construct(Localization $localization) {

		$this->localization = $localization;
	}

	/**
	 * Retrieve a single article based on Mongo ID or url-friendly title
	 * @param  int|string $article_id Mongo ID or url-friendly title
	 * @param  boolean  $id_allowed whether retrieving by id is allowed
	 * @return ArticleModel
	 */
	public function fetchArticle($article_id, $id_allowed = false) {

		$article = ($id_allowed ? ArticleModel::find($article_id) : NULL);

		if (!$article) {

			// try with url_friendly_title
			$article = ArticleModel::where('url_friendly_title', '=', $article_id)
			->where('visible', '=', true);

			/*
			 * Since we don't have enough articles in the Austrian and Swiss sections, we are also
			 * showing German articles in there. This is a quick fix that will be removed later
			 *
			 * Max 08/17/2015
			 */
			$locales = [App::getLocale()];
			if (in_array(App::getLocale(), ['de-CH', 'de-AT'])) {
				$locales[] = 'de-DE';
			}
			$article = $article->whereIn('locale', $locales)->first();

		}

		return $article;
	}

	/**
	 * fetches articles from the databas
	 * @param  array   $filter  [description]
	 * @param  integer $index   [description]
	 * @param  integer $count   [description]
	 * @param  [type]  &$total  [description]
	 * @param  boolean $visible [description]
	 * @return [type]           [description]
	 */
	public function retrieveArticles(array $filter, $index = 0, $count = 0, &$total = null, $visible = true) {

		$articles = ArticleModel::where('visible', '=', $visible);

		/*
		 * Since we don't have enough articles in the Austrian and Swiss sections, we are also
		 * showing German articles in there. This is a quick fix that will be removed later
		 *
		 * Max 08/17/2015
		 */
		$locales = [App::getLocale()];
		if (in_array(App::getLocale(), ['de-CH', 'de-AT'])) {
			$locales[] = 'de-DE';
		}
		$articles->whereIn('locale', $locales);

		$searchString = array_get($filter, 'searchString');
		if (!is_null($searchString) && $searchString !== '') {
           $articles->whereRaw(['$text' => ['$search' => $searchString, '$language' => $this->localization->getLang()]]);
        }

		$total = $articles->count();

		$sorting = (array_get($filter, 'sorting') ?: 'newest_first');

		return $articles->orderBy('date', ($sorting === 'newest_first' ? 'desc' : 'asc'))
			->skip($index)
			->take($count)->get();
	}


	/**
	 * Updates an existing article or creates a new one
	 * @param  array  $fields   [description]
	 * @param  [type] $mongo_id [description]
	 * @return [type]           [description]
	 */
	public function save(array $fields, $mongo_id) {

		$fields['date'] = Carbon::parse($fields['date']);
		$fields['locale'] = App::getLocale();
		$fields['language'] = $this->localization->getLang();
		$fields['url_friendly_title'] = (isset($fields['url_friendly_title']) ? $fields['url_friendly_title'] : Url::makeURLFriendlyString($fields['title']));

		if (!$mongo_id) {
			ArticleModel::create($fields);
		} else {
			$article = ArticleModel::findOrFail($mongo_id);
			$article->fill($fields);
			$article->save();
		}
	}


	public function destroy($mongo_id) {

		ArticleModel::destroy($mongo_id);
	}


	public function setVisibility($visibility, $mongo_id) {

		$article = ArticleModel::findOrFail($mongo_id);
		$article->visible = $visibility;
		$article->save();
	}


}
