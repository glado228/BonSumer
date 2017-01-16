<?php namespace Bonsum\Helpers;

use Auth;
use Bonsum\Services\Resource as ResourceService;
use Lang;

class Resource {


	/**
	 * Given a path to a media resource, returns a complete URL that can be used to access it
	 *
	 *  The URL is determined as follows:
	 *
	 *  1) if $path begins with http:// or is a relative path (i.e. does not star with '/')
	 *  	the corresponding URL is returned
	 *
	 *  2) if $path begins with '/' it is assumed to refer to a media resource
	 *    a) $path begins with '/media/'
	 *      in this case it is assumed to be a fully qualified resource name and a corresponding URL
	 *      (i.e. http://<hostname>/media/<the resource>) will be returned
	 *
	 * 	  b) $path does not begin with '/media/'
	 * 	    in this case we use the $type argument to determine the media type and the resulting final path
	 * 	    and URL. For example, for $type = RESOURCE_TYPE_IMG and $path = '/homepage/background.jpg', the URL
	 * 	    will be:
	 *
	 * 		http://<hostname>/media/img/homepage/background.jpg
	 *
	 * @param string $path the path of the resource
	 * @param int $type the resource type as defined in Bonsum\Services\Resource
	 */
	static public function getMediaURL($path, $type = ResourceService::RESOURCE_TYPE_IMG) {

		if (empty($path)) {
			return $path;
		}

		if (starts_with($path, '/')) {
			if (starts_with($path, '/media/')) {
				$url = $path;
			} else {
				$url = '/media/' . ResourceService::$mediaDirMap[$type] . $path;
			}
		} else {
			if (!starts_with($path, 'http://')) {
				$url = 'http://' . $path;
			} else {
				$url = $path;
			}
		}
		return url($url);
	}


	/**
	 * Wrapper that creates a media resource whose path is editable in place
	 * using AngularJS 	and the xeditable module
	 *
	 * Because this function returns HTML, its output should NOT be HTML-encoded
	 * @param string $id resource id
	 * @param int $type media type
	 * @param boolean secure TRUE if https:// is to be used for the url
	 * @param array $attributes additional attributes for the HTML markup
	 */
	static public function MEDIA($id, $type = ResourceService::RESOURCE_TYPE_IMG, $secure = FALSE, $attributes = []) {

		$auth = app('auth.driver');
		if ($auth->guest() || !$auth->user()->admin) {
			return app('html')->image(self::getMediaURL('/' . app('resources')->getMediaPath($id), $type), (isset($attributes['alt']) ? $attributes['alt'] : NULL), $attributes, $secure);
		}

		$attributes['data-ng-src'] = asset('/media/'. ResourceService::$mediaDirMap[$type] .'/{{resources[\''. $id .'\']}}');
		$attributes['alt'] = $id;
		$html = '<img '.app('html')->attributes($attributes).'>';

		app('frontend')->addResource([$id => app('resources')->getMediaPath($id)], $type);

		return self::generateAngularMarkup($id, $type, $html);
	}


	/**
	 * Create the appropriate Angular HTML markup to enable in-place editing of resources
	 * @param  int $id         	 resource identifier
	 * @param  string $innerHTML optional: html to use for the elemnt
	 * @return string of HTML
	 */
	static private function generateAngularMarkup($id, $type, $innerHTML = '') {

		$tag = 'div';

		return
		'<' . $tag .' data-ng-controller="EditableResourceController"'. ($type == ResourceService::RESOURCE_TYPE_TEXT ? ' data-ng-attr-contenteditable="{{editing()}}"' : '') .'
			  data-ng-click="resourceClicked($event)"
			  ng-attr-id="{{editor_id}}"
			  data-ng-init="init(\''. $id .'\',\''. $type .'\')"
			data-ng-class="{\'missing-resource\': !resources[\''. $id .'\'] && !thisEditorOpen() && !(changesShowing() && hasChanges()), \'editable-clickable\': editing() && !thisEditorOpen() && !changesShowing(),\'highlight-content\': (changesShowing() && hasChanges()) || thisEditorOpen()}"
		  	 title="'. $id .'"'.
		  	 ($type == ResourceService::RESOURCE_TYPE_TEXT ? ' data-ng-bind-html="resources[\''.$id.'\'] || \''. $id .'\'"' : '') . '>'
  			 . $innerHTML
 		. '</' . $tag . '>';
	}

	/**
	 * Wrapper that creates a text resource editable in-place
	 * using AngularJS and the xeditable module
	 *
	 * Because this function returns HTML, its output should NOT be HTML-encoded
	 */
	static public function LR($id, $parameters = array(), $domain = 'messages', $locale = null) {

		$auth = app('auth.driver');
		if ($auth->guest() || !$auth->user()->admin) {
			// escape parameters before passsing them to trans()
			return trans($id, array_map('e', $parameters), $domain, $locale);
		}

		$trans = trans($id, [], $domain, $locale);

		app('frontend')->addResource([$id => (Lang::has($id) ? $trans : NULL)], ResourceService::RESOURCE_TYPE_TEXT);

		return self::generateAngularMarkup($id, ResourceService::RESOURCE_TYPE_TEXT);
	}


}
