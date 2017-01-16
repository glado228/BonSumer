<?php namespace Bonsum\Helpers;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Helper class to generate queries corresponding to angular grid filters
 */
class GridFilter {


	/**
	 *
	 * handles a text filter
	 * @param  QueryBuilder $query        [description]
	 * @param  [type]       $field        [description]
	 * @param  array        $filter_array [description]
	 * @return [type]                     [description]
	 */
	static public function text(QueryBuilder $query, $field, array $filter_array) {

		extract($filter_array);

		switch ($type) {

			case 1:
				$query->where($field, 'like', '%'.$filter.'%');
				break;

			case 2:
				$query->where($field, '=', $filter);
				break;

			case 3:
				$query->where($field, 'like', $filter.'%');
				break;

			case 4:
				$query->where($field, 'like', '%'.$filter);
				break;

		}

		return $query;
	}


	/**
	 * handles a numeric filter
	 * @param  QueryBuilder $query        [description]
	 * @param  [type]       $field        [description]
	 * @param  array        $filter_array [description]
	 * @return [type]                     [description]
	 */
	static public function number(QueryBuilder $query, $field, array $filter_array) {

		extract($filter_array);

		switch ($type) {

			case 1:
				$query->where($field, '=', $filter);
				break;

			case 2:
				$query->where($field, '<', $filter);
				break;

			case 3:
				$query->where($field, '>', $filter);
				break;

		}

		return $query;
	}

	/**
	 * handles a set filter
	 * @param QueryBuilder $query        [description]
	 * @param [type]       $field        [description]
	 * @param array        $filter_array [description]
	 */
	static public function set(QueryBuilder $query, $field, array $filter_array) {

		if (!empty($filter_array)) {
			$query->whereIn($field, $filter_array);
		} else {
			$query->whereNull($field);
		}
	}

}
