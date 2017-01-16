<?php namespace Bonsum\Services;

use App;
use Request;

class FrontEnd {


	const RESOURCE_KEY = 'resources';

	/*
	* CSS files to be included in the header section
	*/
	protected $css = [];

	/*
	* JS to be included in the footer section (default)
	*/
	protected $scripts = [];

	/*
	* JS to be included in the head section
	*/
	protected $head_scripts = [];

	/*
	* inline JS to be included in the footer section (default)
	*/
	protected $inline_scripts = [];

	/*
	* inline JS to be included in the head section
	*/
	protected $inline_head_scripts = [];


	/*
	* vriables to be bassed to the frontend
	*/
	protected $vars = [];

	/**
	 * 	Resources by type
	 */
	protected $resources = [];

	/**
	 * Array of callbacks that will be pushed on piwik JS stack to execute upon page load
	 * @var array
	 */
	protected $piwik_callbacks = [];

	/**
	 * Contains mappings from resource files to their respective cachebusting filenames
	 * @var array
	 */
	protected $manifest = [];


	public function __construct() {

		if (!App::runningInConsole()) {
			$filename = base_path().'/resources/manifest.json';
			$this->manifest = json_decode(file_get_contents($filename), true);

			if ($this->manifest === NULL) {
				throw new \Exception($filename . ' empty or invalid');
			}
		}
	}


	/**
	* create an array with all variables for the JS frontend (including language resources)
	*
	* @return array all JS variables
	*/
	public function makeVars() {
		return array_merge($this->vars, [
			self::RESOURCE_KEY => $this->resources
		]);
	}

	/**
	* create an array with scripts and css for the head section
	*
	* @return array with scripts and css
	*/
	public function makeHead() {
		return [
			'scripts' => $this->head_scripts,
			'css' => $this->css,
			'inline_scripts' => $this->inline_head_scripts
		];
	}

	/**
	* create an array with scripts, css, and piwik data (tracking) for the footer section
	*
	* @return array with scripts and css
	*/
	public function makeFooter() {

		$piwik_site_id = array_get(config('piwik.site_id'), Request::getHost());

		return [
			'scripts' => $this->scripts,
			'inline_scripts' => $this->inline_scripts,
			'piwik_callbacks' => $this->piwik_callbacks,
			'piwik_site_id' => $piwik_site_id
		];
	}


	/**
	 * Pass variables to the frontend
	 *
	 * @param array $vars array of variables to pass
     */
	public function addVars(array $vars) {

		foreach ($vars as $key => $val) {
			$this->vars[$key] = $val;
		}
	}

	/**
	 * Pass variables to the resource object in the frontend
	 *
	 * @param array $vars array of variables to pass
	 * @param int $type resrouce type
     */
	public function addResource(array $vars, $type) {

		if (!isset($this->resources[$type])) {
			$this->resources[$type] = [];
		}
		foreach ($vars as $key => $val) {
			$this->resources[$type][$key] = $val;
		}
	}

	/**
	 * Add an inline script
	 * @param string  $script script content
	 * @param boolean $head   true if adding to head
	 */
	public function addInlineScript($script, $head = FALSE) {

		if ($head) {
			$this->inline_head_scripts[] = $script;
		} else {
			$this->inline_scripts[] = $script;
		}
	}

	/**
	 * Queue a script for inclusion in the output view
	 *
	 * @param string|array $scriptfile name(s) of the script files in public/js
	 * @param bool $head wether the script has to be included in the head section (default will be footer)
     */
	public function addScript($scriptfile, $head = FALSE) {

		$all_scripts = array_merge($this->scripts, $this->head_scripts);

		if ($head) {
			$dest = &$this->head_scripts;
		} else {
			$dest = &$this->scripts;
		}

		$scriptfile = (array) $scriptfile;
		foreach ($scriptfile as $script) {
			if (!in_array($script, $all_scripts)) {
				$dest[] = $this->fromManifest($script);
			}
		}
	}

	/**
	 * Queue a css file for inclusion in the output view
	 *
	 * @param string|array $cssfile name(s) of the css files in public/css
     */
	public function addCss($cssfile	) {

		$cssfile = (array) $cssfile;
		foreach ($cssfile as $css) {
			if (!in_array($css, $this->css)) {
				$this->css[] = $this->fromManifest($css);
			}
		}
	}

	public function addPiwikCallback($callback_name, array $params = []) {

		$this->piwik_callbacks[] = array_merge([$callback_name], $params);
	}

	/**
	 * [addPiwikGoal description]
	 * @param [type] $goal_number [description]
	 * @param array  $params      [description]
	 */
	public function addPiwikGoal($goal_number, array $params = []) {

		$this->addPiwikCallback('trackGoal', array_merge([$goal_number], $params));
	}

	/**
	 * Get a resource name from the manifest, if it exists. Otherwise the resource name is returned unchanged
	 * @param  [type] $resourceName [description]
	 * @return [type]               [description]
	 */
	public function fromManifest($resourceName) {

		return (empty($this->manifest[$resourceName]) ? $resourceName : $this->manifest[$resourceName]);
	}

}
