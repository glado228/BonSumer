<?php

use Bonsum\Services\Resource as ResourceService;

class ResourceTest extends TestCase {

	public function setUp() {

		parent::setUp();

		$this->app->make('session')->start();
	}

	public function testAddTextResource() {
		$user = new \Bonsum\User();
		$user->admin = TRUE;

		$this->be($user);

		$textres = str_random(200);

		$this->action(
			'POST',
			'ResourceController@postUpdateResources',
			[],
			[
				ResourceService::RESOURCE_TYPE_TEXT => [
					'test.resource' => $textres
				]
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseOk();
		$this->assertEquals($textres, trans('test.resource'));
	}


	public function testAddTextResourceWithScriptTags() {
		$user = new \Bonsum\User();
		$user->admin = TRUE;

		$this->be($user);

		$this->action(
			'POST',
			'ResourceController@postUpdateResources',
			[],
			[
				ResourceService::RESOURCE_TYPE_TEXT => [
					'test.resource' => str_random(200) . '<script' . str_random(200)
				]
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(400);
	}

	public function testAddTextResourceUnauthenticatd() {

		$this->action(
			'POST',
			'ResourceController@postUpdateResources',
			[],
			[
				ResourceService::RESOURCE_TYPE_TEXT => [
					'test.resource' => str_random(200)
				]
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(302);
	}

	public function testAddTextResourceAsOrdinaryUser() {

		$user = new \Bonsum\User();

		$this->be($user);

		$this->action(
			'POST',
			'ResourceController@postUpdateResources',
			[],
			[
				ResourceService::RESOURCE_TYPE_TEXT => [
					'test.resource' => str_random(200)
				]
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(403);
	}

	public function testAddImageResource() {
		$user = new \Bonsum\User();
		$user->admin = TRUE;

		$this->be($user);

		$mediapath = str_random(200);

		$this->action(
			'POST',
			'ResourceController@postUpdateResources',
			[],
			[
				ResourceService::RESOURCE_TYPE_IMG => [
					'test.resource' => $mediapath
				]
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseOk();
		$this->assertEquals($mediapath, $this->app->make('resources')->getMediaPath('test.resource'));
	}

}