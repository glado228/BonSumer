<?php namespace Bonsum\Services;

use Illuminate\Translation\FileLoader;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;
use Carbon\Carbon;
use Auth;
use Request;

class Resource {

	/**
	 * media types
	 */
	const RESOURCE_TYPE_TEXT = 0;
	const RESOURCE_TYPE_IMG = 1;

	/**
	 * regexp used for sanity checks
	 */
	const SANITIZE_REGEXP = '<\/?script';

	/**
	 * mapping of media type to corresponding direcotry
	 * @var array
	 */
	public static $mediaDirMap = [
		self::RESOURCE_TYPE_IMG => 'img'
	];

	/**
	 * translator instance used to manage text resource
	 * @var Illuminate\Translation\Translator
	 */
	protected $translator = NULL;

	/**
	 * Localization service
	 * @var Bonsum\Service\Localization
	 */
	protected $localization = NULL;

	/**
	 * Service used to load localized files
	 * @var Illuminate\Translation\Fileloader
	 */
	protected $fileLoader = NULL;

	/**
	 * Interface to filesystem
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem = NULL;

	/**
	 * array of loaded media groups
	 * @var array
	 */
	protected $mediaLoaded = [];


	public function __construct() {

		// add a namespace in the loader for each media type directory*/
		$this->translator = app('translator');
		$this->filesystem = app('files');
		$this->fileLoader = app('translation.loader');
		$this->localization = app('localization');
		foreach (self::$mediaDirMap as $namespace) {
			$this->fileLoader->addNamespace($namespace, self::getNamespacePath($namespace));
		}
	}

	/**
	 * Get the namespace path for a media type
	 * @param  [type] $type [description]
	 * @return [type]       [description]
	 */
	public static function getNamespacePath($namespace) {
		return app('path.media') . '/' . $namespace;
	}

	/**
	 * get all the supported resource types
	 * @return array array of resource types
	 */
	public static function getTypes() {
		return [
			'text' => self::RESOURCE_TYPE_TEXT,
			'img' => self::RESOURCE_TYPE_IMG
		];
	}

	/**
	 * group resources by group :)
	 * @param  array $resources input resources
	 * @return array grouped resources
	 * @throws BadRequestHttpException If the value of some resource maches SANITIZE_REGEXP
	 */
	protected function groupResources(array $resources) {

		// group the resources by group (i.e. file)
		$items_by_group = [];
		foreach ($resources as $key => $val) {

			if (preg_match('/'. self::SANITIZE_REGEXP .'/', $val)) {
				throw new BadRequestHttpException('Resources contain forbidden control sequences.');
			}

			list($namespace, $group, $item) = $this->translator->parseKey($key);
			if (!isset($items_by_group[$group])) {
				$items_by_group[$group] = [];
			}
			$items_by_group[$group][$item] = $val;
		}

		return $items_by_group;
	}

	/**
	 * update resources
	 * @param  array resources
	 * @param  int resource type
	 * @return void
	 * @throws Exception If resource type is invalid
	 */
	public function update(array $resources, $type) {

		$namespace = ($type == self::RESOURCE_TYPE_TEXT ? NULL : self::$mediaDirMap[$type]);
		$locale = app()->getLocale();
        $base_path = ($type == self::RESOURCE_TYPE_TEXT ? app('path.lang') : self::getNamespacePath(self::$mediaDirMap[$type]));

		$grouped_resources = $this->groupResources($resources);

		foreach ($grouped_resources as $group => $items) {
			$group_content = $this->fileLoader->load(app()->getLocale(), $group, $namespace); // get the array

			foreach ($group_content as $key => $value) {
				unset($group_content[$key]);
				array_set($group_content, $key, $value);
			}

			foreach ($items as $key => $value) {
				if (is_null($value)) {
					// storing keys with null values means => unset the key
					unset($group_content[$key]);
				} else {
					array_set($group_content, $key, $value);
				}
			}
			$group_dir = $base_path. "/{$locale}";
			if (!$this->filesystem->exists($group_dir)) {
				$this->filesystem->makeDirectory($group_dir);
			}
			$group_path = "{$group_dir}/{$group}.php";
			$file_content = "<?php\n\n"
			. "/*\n"
			. " *\tUpdated by: ". Auth::user()->fullname . " (". Auth::user()->email . ")\n"
			. " *\tOn: " . Carbon::now()->toRfc850String() . "\n"
			. " *\tVia web admin-interface from: ". Request::getClientIp() ."\n"
			. " */\n\nreturn "
			. var_export($group_content, TRUE) . ";\n";
			$this->filesystem->put($group_path, $file_content, TRUE);
		}
	}


	/**
	 * Whether a media group has already been loaded
	 * @param  int $type media type
	 * @param  string $group group name
	 * @return boolean true if the media group has already been loaded
	 */
	protected function isMediaLoaded($locale, $group, $namespace) {

		return isset($this->mediaLoaded[$namespace][$group][$locale]);
	}

	/**
	 * load, cache and return the media group
	 * @param  int $type media type
	 * @param  string $group group name
	 * @return void
	 */
	protected function loadMediaGroup($locale, $group, $namespace) {

		if (!$this->isMediaLoaded($locale, $group, $namespace)) {
			$this->mediaLoaded[$namespace][$group][$locale] = $this->fileLoader->load($locale, $group, $namespace);
		}

		return $this->mediaLoaded[$namespace][$group][$locale];
	}


	/**
	 * get the media path associated to a given id (key)
	 * @param  string $id the key
	 * @param  int $type the type
	 * @return string the media path
	 */
	public function getMediaPath($id, $locale = null, $type = self::RESOURCE_TYPE_IMG) {

		if ($locale === NULL) {
			$locale = app()->getLocale();
		}

		$namespace = self::$mediaDirMap[$type];

		list($n, $group, $item) = $this->translator->parseKey($id);
		$fallback_locale = $this->localization->getFallbackLocale($locale, true);
		if (!$fallback_locale) {
			$fallback_locale = '';
		}

		$try_locales = [
			$locale, // the current locale
			$fallback_locale, // an explicit fallback
			'' // the empty locale
		];

		foreach ($try_locales as $locale) {
			$media = $this->loadMediaGroup($locale, $group, $namespace);
			if (array_has($media, $item)) {
				return array_get($media, $item);
			}
		}

		return NULL;
	}


}
