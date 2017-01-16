<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bonsum\MongoDB\Shop;

class ImportShops extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'import:shops';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$json_file = $this->argument('input-file');
		$this->comment($json_file);

		$shops = json_decode(file_get_contents($json_file));

		$skipped = 0;
		$imported = 0;
		$updated = 0;

		foreach ($shops as $entry) {

			if ($this->option('update-links')) {


				$shop = Shop::where('shop_id', '=', intval($entry->shop_id))->first();

				if ($shop) {

					// adjust affilinet network name
					if ($shop->affiliate == "affilinet") {
						$shop->affiliate = "affilinet_1";
					}

					$shop->link = $entry->link;
					$shop->save();

					$this->comment('udpated link for '  . $shop->name);
					$updated++;

				} else {

					$this->error('could not find shop '. $entry->shop_name. '. No link updated');
				}


			} else if (!Shop::where('shop_id', '=', intval($entry->shop_id))->exists()) {

				$shop = new Shop;
				$this->error($entry->shop_name);

				$shop->name = $entry->shop_name;
				$shop->shop_id = intval($entry->shop_id);
				$shop->visible = boolval($entry->shop_status);
				$shop->description = explode("\r\n", $entry->shop_description)[0];
				$shop->popularity = intval($entry->popularity);
				$shop->language = 'de';

				$entry->icon_data = unserialize($entry->icon_data);
				$shop->shop_criteria = array_values($entry->icon_data);

				$entry->affiliate_data = unserialize($entry->affiliate_data);
				foreach ($entry->affiliate_data as $network => $value) {
					if ($value) {
						if ($shop->affiliate) {
							$this->error('2 affiliate networks');
						}
						$shop->affiliate = ($network == 'affili' ? 'affilinet' : $network);
					}
				}

				switch ($entry->comission_type) {
					case 0:
						$shop->reward_type = Shop::REWARD_TYPE_NO_REWARD;
						break;
					case 1:
						$shop->reward_type = Shop::REWARD_TYPE_PROPORTIONAL;
						$shop->proportional_reward = intval($entry->comission_size);
						break;
					case 2:
						$shop->reward_type = Shop::REWARD_TYPE_FIXED;
						$shop->fixed_reward = intval($entry->comission_size);
						break;

					default:
						dd('error - unknown commission_type ' . $entry->comission_type);
				}

				$shop->link = $entry->link;

				$escaped_name = str_replace('/', '-', str_replace(' ', '_', $shop->name));
				$shop->thumbnail = $this->downloadImage($entry->image, '/shops/logos/' . $escaped_name . '_thumbnail_1');
				$shop->thumbnail_mouseover = $this->downloadImage($entry->active_image, '/shops/logos/' . $escaped_name . '_thumbnail_2');

				$this->comment($shop->thumbnail);
				$this->comment($shop->thumbnail_mouseover);

				$shop->save();
				$imported++;

				$this->comment('created '. $shop->name);
			} else {
				$skipped++;
				$this->comment('skipped '. $entry->shop_name);
			}
		}

		$this->info('total '. count($shops));
		$this->info('imported '. $imported);
		$this->info('updated '. $updated);
		$this->info('updated '. $skipped);

	}

	protected function downloadImage($url, $path) {

		$url_info = parse_url($url);

		$ext = pathinfo($url_info['path'], PATHINFO_EXTENSION);
		$destfile = './public/media/img' . $path . '.' . $ext;

		if ($this->option('download-images')) {
			try {
				file_put_contents($destfile, fopen('http://www.bonsum.de' . $url_info['path'], 'r'));
			} catch (\Exception $e) {
				$this->error($e);
			}
		}
		return $path. '.'. $ext;
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['input-file', InputArgument::REQUIRED, 'JSON file to import data from.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['update-links', NULL, InputOption::VALUE_NONE, 'Only update links, don\’t create any new shops.'],
			['download-images', NULL, InputOption::VALUE_NONE, 'Download images.']
		];
	}

}
