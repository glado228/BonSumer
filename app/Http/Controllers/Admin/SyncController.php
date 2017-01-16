<?php namespace Bonsum\Http\Controllers\Admin;

use Bonsum\Http\Controllers\Controller;
use Bonsum\Services\FrontEnd;
use Illuminate\Auth\Guard;
use Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/*
	provides an admin interface to synchronize resources (media files like images and later, language resources)
	to the production server
 */


class SyncController extends Controller {

	/**
	 * Command uses to sync the media
	 * @var [type]
	 */
	protected $sync_media_cmd;

	/**
	 * Lock file location
	 * @var [type]
	 */
	protected $sync_media_lock_file;


	protected $auth;


	public function __construct(Guard $auth) {

		$this->middleware('admin');

		$this->auth = $auth;
		$this->sync_media_cmd = config('app.sync_media_cmd');
		$this->sync_media_lock_file = sys_get_temp_dir() . '/sync_media';
	}



	public function index(FrontEnd $fe) {

		$fe->addVars(
			[
				'syncMediaUrl' => action('Admin\SyncController@syncMedia'),
				'syncMediaLockUrl' => action('Admin\SyncController@checkSyncMediaLock')
			]
		);

		return view('admin.sync');
	}

	public function checkSyncMediaLock() {

		return $this->mediaLocked();
	}


	public function syncMedia() {

		ini_set('max_execution_time', 300); //300 seconds = 5 minutes

		$response_body = '';

		if (!$this->sync_media_cmd) {
			return response('You need to specify a command to use to sync media in the SYNC_MEDIA_CMD environment variable', 500);
		}

		try {
			$lock = $this->lockMedia();

			$proc = proc_open($this->sync_media_cmd, [
				1 => ['pipe', 'w'],
				2 => ['pipe', 'w']
			], $pipes);

			if ($proc === FALSE) {
				return response('call to proc_open failed', 500);
			}

			$output = stream_get_contents($pipes[1]);
			$error = stream_get_contents($pipes[2]);

			fclose($pipes[1]);
			fclose($pipes[2]);

			$response_body = $output.$error;

			$ret = proc_close($proc);
			if ($ret) {
				return response($response_body, 500);
			}

		} finally {
			$this->unlockMedia($lock);
		}

		return $response_body;
	}

	protected function mediaLocked() {

		$content = '';
		if (file_exists($this->sync_media_lock_file)) {
			$fh = fopen($this->sync_media_lock_file, 'r');
			if (!flock($fh, LOCK_EX|LOCK_NB)) {
				$content = stream_get_contents($fh);
			}
			fclose($fh);
		}
		return $content;
	}

	protected function unlockMedia($fh) {

		fclose($fh);
		unlink($this->sync_media_lock_file);
	}

	protected function lockMedia() {

		$fh = fopen($this->sync_media_lock_file, 'w');
		flock($fh, LOCK_EX);
		fwrite($fh, $this->auth->user()->email);
		fflush($fh);
		return $fh;
	}


}
