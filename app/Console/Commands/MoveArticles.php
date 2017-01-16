<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MoveArticles extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'articles:move';

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
		$articles = \Bonsum\Article::all();

		$this->info('Found ' . $articles->count() . ' articles');

		$it = $articles->getIterator();

		$cur = 1;
		while ($it->valid()) {
			$this->info('moving article ' . $cur);
			$from = $it->current();
			$to = new \Bonsum\MongoDB\Article(
				[
					'title' => $from->title,
					'date' => $from->date,
					'authors' => $from->authors,
					'tags' => $from->tags,
					'image' => $from->image,
					'locale' => $from->locale,
					'visible' => (boolean) $from->visible,
					'body' => $from->body,
					'description' => $from->description
				]
			);
			$to->save();
			$it->next();
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
		];
	}

}
