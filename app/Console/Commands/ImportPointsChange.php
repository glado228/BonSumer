<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bonsum\BonetsCredit;
use Bonsum\BonetsDonation;
use Bonsum\BonetsRedeem;
use Bonsum\Services\Bonets;

class ImportPointsChange extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'import:points';

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
	public function __construct(Bonets $bonets)
	{
		parent::__construct();

		$this->bonets = $bonets;
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


		$voucher_file = $this->argument('vouchers');
		$vouchers = json_decode(file_get_contents($voucher_file));

		$voucher_map = [];
		foreach ($vouchers as $voucher) {
			$voucher_map[$voucher->voucher_id] = $voucher;
		}

		$json_file = $this->argument('input-file');

		$this->info($json_file);
		$records = json_decode(file_get_contents($json_file));


		$imported = 0;
		$errors = 0;

		foreach ($records as $record) {


			$new_record = NULL;

			if ($record->way === '+') {

				$new_record = new BonetsCredit([
					'user_id' => $record->user_id,
					'bonets' => $record->points_change,
					'date' => $record->time,
					'description' => $record->comment
				]);

			} else {

				if (empty($voucher_map[$record->voucher_id])) {
					++$errors;
					$this->error('could not find a voucher with ID ' . $record->voucher_id .' for wp_points_change record with ID ' . $record->id);
					continue;
				}


				$voucher = $voucher_map[$record->voucher_id];

				if ($voucher->type_id == 1) {

					$new_record = new BonetsRedeem([
						'user_id' => $record->user_id,
						'bonets' => $record->points_change,
						'date' => $record->time,
						'amount' => $this->bonets->fromBonets($record->points_change),
						'currency' => 'EUR',
						'shop_id' => '',
						'voucher_code' => ''
					]);

				} else {

/*
					This code was meant to recover the receiver organisation of old donations
					in the new site. Turns out there are none.

					$donation = \Bonsum\MongoDB\Donation::where('old_voucher_id', '=', intval($record->voucher_id))->first();
					if (!$donation) {
						$this->error('could not find old_voucher_id = ' . $record->voucher_id);
						$skipped[] = $record;

					} else {*/

					$new_record = new BonetsDonation([

						'user_id' => $record->user_id,
						'bonets' => $record->points_change,
						'date' => $record->time,
						'amount' => $this->bonets->fromBonets($record->points_change),
						'currency' => 'EUR',
						'donation_id' => '',
					]);
					//}

				}

			}

			try {
				$new_record->save();
				$imported++;
			} catch (\Exception $e) {
				$this->error($e->getMessage());
				$errors++;
			}
		}

		$this->info('total '. count($records));
		$this->info('imported '. $imported);
		$this->info('errors '. $errors);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['input-file', InputArgument::REQUIRED, 'JSON dump of wp_points_change.'],
			['vouchers', InputArgument::OPTIONAL, 'JSON dump of wp_vouchers.']
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
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
