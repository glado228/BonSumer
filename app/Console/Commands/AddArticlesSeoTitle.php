<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bonsum\MongoDB\Article;
use Bonsum\Helpers\Url;

class AddArticlesSeoTitle extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seo:article';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add the SEO URL-friendly title to the existing articles and set them as available at top level';

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
		$force = $this->option('force');
		$top_level = $this->option('top-level');
		$titles_written = 0;
		$flags_written = 0;

		Article::all()->each(function ($a) use ($force, $top_level, &$titles_written, &$flags_written) {

			if (is_null($a->url_friendly_title) || $force) {
				$a->url_friendly_title = Url::makeUrlFriendlyString($a->title);
				$titles_written++;
			}
			if (!is_null($top_level)) {
				$a->available_top_level = boolval($top_level);
				$flags_written++;
			}
			$a->save();
		});

		$this->info('Total: '. Article::all()->count());
		$this->info('URL-friendly titles updated: '. $titles_written);
		$this->info('Top-level flags updated: '. $flags_written);


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
			['force', null, InputOption::VALUE_NONE, 'Overwrite existing URL-friendly titles.', null],
			['top-level', null, InputOption::VALUE_REQUIRED, 'Set the available at top level flag.', null],
		];
	}

}
