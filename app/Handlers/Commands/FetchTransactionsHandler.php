<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\FetchTransactions as FetchTransactionsCommand;
use Bonsum\Services\MerchantTransactions as MerchantTransactionsService;
use Illuminate\Queue\InteractsWithQueue;
use Bonsum\Events\MerchantTransactionsDownload;


class FetchTransactionsHandler {

	/**
	 *
	 * @var Bonsum\Services\MerchantTransactions
	 */
	protected $mts;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(MerchantTransactionsService $mts)
	{
		$this->mts = $mts;
	}

	/**
	 * Handle the command.
	 *
	 * @param  FetchTransactions  $command
	 * @return void
	 */
	public function handle(FetchTransactionsCommand $command)
	{
		$start_time = microtime(TRUE);
		list($transaction_counter, $error_counter) = $this->mts->fetchTransactions($command->from, $command->to, $command->save, $command->networks);
		$total_time = microtime(TRUE) - $start_time;

		event(new MerchantTransactionsDownload($total_time, $transaction_counter, $error_counter, $command->from, $command->to, $command->save));
	}

}
