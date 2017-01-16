<?php namespace Bonsum\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB;
use Bonsum\User;

class ImportUsers extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'import:users';

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
		User::unguard();

		$json_file = $this->argument('input-file');
		$this->info($json_file);

		$users = json_decode(file_get_contents($json_file));

		$skipped = 0;
		$imported = 0;
		$overwritten = 0;

		foreach ($users as $user) {

			$skip = false;

			if (!ends_with($user->user_email, 'bonsum.de')) {

				$skip = DB::table('users')->where('email', '=', $user->user_email)->exists();

				if ($skip) {
					$skipped++;
				}

				if (!$skip) {
					$names = preg_split("/\s+/", $user->user_login, 2, PREG_SPLIT_NO_EMPTY);

					User::create([
						'id' => $user->ID,
						'password' => bcrypt(str_random(40)),
						'old_password' => $user->user_pass,
						'firstname' => $names[0],
						'lastname' => (count($names) > 1 ? $names[1] : ''),
						'email' => $user->user_email,
						'confirmed' => true,
						'admin' => false,
						'disabled' => false
					]);

					$imported++;
				}
			} else {
				$skipped++;
			}
		}

		$this->info('total '. count($users));
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
			['input-file', InputArgument::REQUIRED, 'JSON input file.'],
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
