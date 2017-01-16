<?php

use Symfony\Component\DomCrawler\Crawler;
use Bonsum\Services\Resource as ResourceService;

class FrontEndTest extends TestCase {


	public function testCSS() {

		$this->app->make('frontend')->addCSS('foo.css');
		$this->app->make('frontend')->addCSS('goo.css');
		$this->app->make('frontend')->addCSS(['foo.css', 'goo.css']);
		$this->app->make('frontend')->addCSS(['foo.css', 'goo.css']);

		$crawler = new Crawler($this->action('GET', 'HomeController@index')->getContent());

		$this->assertCount(2, $crawler->filter('head > link')->reduce(function($node) {
			return ends_with($node->attr('href'), ['foo.css', 'goo.css']);
		}));
	}

	public function testScriptHead() {

		$this->app->make('frontend')->addScript('foo.js', TRUE);
		$this->app->make('frontend')->addScript('goo.js', TRUE);
		$this->app->make('frontend')->addScript(['foo.js', 'goo.js'], TRUE);
		$this->app->make('frontend')->addScript(['foo.js', 'goo.js']);

		$crawler = new Crawler($this->action('GET', 'HomeController@index')->getContent());

		$this->assertCount(2, $crawler->filter('head > script')->reduce(function($node) {
			return ends_with($node->attr('src'), ['foo.js', 'goo.js']);
		}));
	}

	public function testScriptFooter() {

		$this->app->make('frontend')->addScript('foo.js');
		$this->app->make('frontend')->addScript('goo.js');
		$this->app->make('frontend')->addScript(['foo.js', 'goo.js'], TRUE);
		$this->app->make('frontend')->addScript(['foo.js', 'goo.js']);

		$crawler = new Crawler($this->action('GET', 'HomeController@index')->getContent());

		$this->assertCount(2, $crawler->filter('footer > script')->reduce(function($node) {
			return ends_with($node->attr('src'), ['foo.js', 'goo.js']);
		}));
	}

	public function testVars() {

		$this->app->make('frontend')->addVars(['foo' => 1]);
		$this->app->make('frontend')->addVars(['foo' => 1]);

		$crawler = new Crawler($this->action('GET', 'HomeController@index')->getContent());

		$this->assertCount(1, $crawler->filter('script')->reduce(function($node) {
			return substr_count($node->html(), 'foo =') == 1;
		}));
	}

	public function testLangRes() {

		$this->app->make('frontend')->addResource(['foo.goo' => 'hello'], ResourceService::RESOURCE_TYPE_TEXT);
		$this->app->make('frontend')->addResource(['foo.goo' => 'hello'], ResourceService::RESOURCE_TYPE_TEXT);

		$crawler = new Crawler($this->action('GET', 'HomeController@index')->getContent());

		$this->assertCount(1, $crawler->filter('script')->reduce(function($node) {
			return substr_count($node->html(), '"foo.goo":"hello"') == 1;
		}));
	}

}