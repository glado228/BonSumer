<?php namespace Bonsum\Helpers;

use SplTempFileObject;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CSV {

	/**
	 * Stream the chunked results of a query to a CSV  in a memory efficient mannner
	 * @param  QueryBuilder $query     The query that will give us our results
	 * @param  string       $file_name The file name
	 * @param  array restrict to these columns
	 * @return StramResponse                  a response
	 */
	static public function sendCsvFileFromQuery(QueryBuilder $query, $file_name, $columns = null) {

		$file_name .= (!ends_with($file_name, '.csv') ? '.csv' : '');

		$response = new StreamedResponse(NULL, 200, [
			'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$file_name.'"'
		]);

		if (is_array($columns)) {
			$columns = array_flip($columns);
		}

		$response->setCallback(function() use ($query, $columns) {

			$out = fopen('php://output', 'w');
			$header_sent = false;

			$query->chunk(500, function($results) use ($out, $columns, &$header_sent) {

				foreach ($results as $row) {

					$data = $row->toArray();

					if (is_array($columns)) {
						$data = array_intersect_key($data, $columns);
					}

					if (!$header_sent) {

						fputcsv($out, array_keys($data), ';');
						$header_sent = true;
					}


					fputcsv($out, $data, ';');
				}
			});

			fclose($out);
		});

		return $response;
	}

	/**
	 * Send a CSV file from a collection
	 * @param  Collection $items     [description]
	 * @param  [type]     $file_name [description]
  	 * @param  array restrict to these columns
	 * @return [type]                [description]
	 */
	static public function sendCsvFileFromCollection(Collection $items, $file_name, $columns = null) {

		$file_name .= (!ends_with($file_name, '.csv') ? '.csv' : '');

		$response = new StreamedResponse(NULL, 200, [
			'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$file_name.'"'
		]);

		if (is_array($columns)) {
			$columns = array_flip($columns);
		}

		$response->setCallback(function() use ($items, $columns) {

			$out = fopen('php://output', 'w');

			$header_sent = false;

			foreach ($items as $item) {

				$data = $item->toArray();

				if (is_array($columns)) {
					$data = array_intersect_key($data, $columns);
				}

				if (!$header_sent) {
					fputcsv($out, array_keys($data), ';');
					$header_sent = true;
				}

				fputcsv($out, $data, ';');
			}

			fclose($out);
		});

		return $response;
	}

}
