<?php

use Bonsum\User;
use Carbon\Carbon;
use Bonsum\MongoDB\Article;
use Bonsum\Helpers\Url;

class ArticleTest extends TestCase {


	public function setUp() {

		parent::setUp();

		$title = str_random(40);
		$this->article = Article::create([
				'visible' => false,
				'date' => Carbon::now(),
				'title' => $title,
				'description' => str_random(10),
				'url_friendly_title' => Url::makeUrlFriendlyString($title),
				'body' => str_random(10),
				'authors' => [['text' => str_random(10)]],
				'tags' => [['text' => str_random(10)]],
				'language' => 'hu',
				'locale' => 'hu-HU',
 				'visible' => false
		]);

		$this->app->make('session')->start();
		$this->app->session->set('locale', 'hu-HU');

	}


	public function tearDown() {

		$this->article->delete();
	}

	public function testAddArticleUnauthenticated() {

		$this->action(
			'POST',
			'ArticleController@store',
			[],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(401);
	}

	public function testAddArticleNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'POST',
			'ArticleController@store',
			[],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(403);
	}

	public function testViewHiddenArticlesNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'GET',
			'ArticleController@indexInvisible'
		);

		$this->assertResponseStatus(403);
	}

	public function testViewHiddenArticleNoAdmin() {



		$user = new User();
		$this->be($user);

		$this->action(
			'GET',
			'ArticleController@show',
			[$this->article->id]
		);

		$this->assertResponseStatus(404);

	}

	public function testViewArticleByTitle() {

		$this->article->visible = true;
		$this->article->save();

		$this->action(
			'GET',
			'ArticleController@show',
			[$this->article->url_friendly_title]
		);

		$this->assertResponseStatus(200);

	}

	public function testViewArticleByTitleWrongLocale() {

		$this->article->visible = true;
		$this->article->locale = "fr-FR";
 		$this->article->save();

		$this->action(
			'GET',
			'ArticleController@show',
			[$this->article->url_friendly_title]
		);

		$this->assertResponseStatus(404);
	}

	public function testViewHiddenArticleByTitle() {

		$this->action(
			'GET',
			'ArticleController@show',
			[$this->article->url_friendly_title]
		);

		$this->assertResponseStatus(404);
	}

	public function testViewArticleTopLevel() {

		$this->article->visible = true;
		$this->article->available_top_level = true;
		$this->article->save();

		$this->action(
			'GET',
			'HomeController@index',
			[$this->article->url_friendly_title]
		);

		$this->assertResponseStatus(301);
	}


	public function testAddArticleWithScriptTags() {

		$user = new User();
		$user->admin = true;
		$this->be($user);

		$this->action(
			'POST',
			'ArticleController@store',
			[],
			[
				'title' => str_random(10),
				'date' => Carbon::now(),
				'description' => str_random(10),
				'body' => str_random(20) . '<script>' . str_random(50),
				'tags' => [['text' => 'hello']],
				'authors' => [['text' => 'max']]
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(422);
		$this->assertArrayHasKey('body', $this->response->getData(true));
	}

}
