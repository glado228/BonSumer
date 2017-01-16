<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carbon\Carbon;

class FetchTransactions extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'update:transactions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fetch the transactions for a user from our affiliate networks.';

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
	public function fire(\Bonsum\Services\MerchantTransactions $mt, \Illuminate\Contracts\Bus\Dispatcher $dispatcher)
	{
		$from = $this->argument('from');
		if ($from) {
			$from = Carbon::parse($from);
		}
		$to = $this->argument('to');
		if (!$to) {
			$to = Carbon::today();
		} else {
			$to = Carbon::parse($to);
		}

		$days = $this->option('days');
		if ($days) {
			if ($from) {
				$this->error('If you use "--days", then you cannot specify either "from"');
				return;
			}
			$from = Carbon::today()->subDays($days);
		}

		$networks = null;
		$network_option = $this->option('networks');
		if ($network_option) {
			$networks = explode(',', $network_option);
		}

		$this->info('fetching transactions from: ' . $from->toDateString() . ' to: ' . $to->toDateString());
		if ($this->option('save')) {
			$dispatcher->dispatch(new \Bonsum\Commands\FetchTransactions(
				$from, $to, true, $networks
			));
		} else {
			$this->info('(transactions will be printed out and not saved)');
			$trs = $mt->fetchTransactions($from, $to, false, $networks);
			foreach ($trs as $tr) {
				$this->info(json_encode($tr, JSON_PRETTY_PRINT));
			}
		}

		if ($this->option('bonets')) {
			$this->info('updating bonets');
			$dispatcher->dispatch(new \Bonsum\Commands\UpdateBonets());
		}
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['from', InputArgument::OPTIONAL, 'transactions from this date.'],
			['to', InputArgument::OPTIONAL, 'transactions to this date. Usees current date if omitted'],
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
			['days', 'd', InputOption::VALUE_OPTIONAL, 'Fetch up do this many days in the past.', null],
			['save', 's', InputOption::VALUE_NONE, 'Save transactions in the database.', null],
			['bonets', 'b', InputOption::VALUE_NONE, 'Update bonets after fetching transactions.', null],
			['networks', 'w', InputOption::VALUE_OPTIONAL, 'Only fetch from these networks.', null]
		];
	}

}
