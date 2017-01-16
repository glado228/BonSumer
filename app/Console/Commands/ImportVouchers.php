<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bonsum\MongoDB\Donation;

class ImportVouchers extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'import:vouchers';

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
		if (!$this->confirm('This operation may add duplicate records. Do you want to continue?')) {
			return;
		}

		$json_file = $this->argument('input-file');
		$this->info($json_file);

		$imported = 0;
		$skipped = 0;

		$vouchers = json_decode(file_get_contents($json_file));

		foreach ($vouchers as $entry) {

			if ($entry->type_id == 1) {
				$skipped++;
				continue;
			}

			if (Donation::where('old_voucher_id', '=', intval($entry->voucher_id))->exists()) {
				$skipped++;
				continue;
			}

			$donation = new Donation();
			$donation->donation_sizes = [intval($entry->price)];
			$donation->old_voucher_id = intval($entry->voucher_id);
			$donation->name = "";
			$donation->description = $entry->substring;
			$donation->language = 'de';
			$donation->visible = boolval($entry->status);
			$donation->thumbnail = $entry->image ? $this->downloadImage($entry->image) : NULL;
			$donation->thumbnail_mouseover = NULL;
			$donation->popularity = 0;

			$donation->save();

			$imported++;
		}

		$this->info('total '. count($vouchers));
		$this->info('imported '. $imported);
		$this->info('skipped '. $skipped);
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
			['download-images', null, InputOption::VALUE_NONE, 'Whether to download images.', null],
		];
	}


	protected function downloadImage($url) {

		$url_info = parse_url($url);
		$basename = pathinfo($url_info['path'], PATHINFO_BASENAME);
		$host = !empty($url_info['host']) ? $url_info['scheme'] . '://' . $url_info['host'] : 'http://www.bonsum.de';

		$mediapath = '/donations/logos/' . $basename;
		$destfile = './public/media/img' . $mediapath;

		if ($this->option('download-images')) {
			try {
				file_put_contents($destfile, fopen($host . $url_info['path'], 'r'));
			} catch (\Exception $e) {
				$this->error('URL = ' . $host . $url_info['path']);
				$this->error($e);
			}
		}
		return $mediapath;
	}
}
