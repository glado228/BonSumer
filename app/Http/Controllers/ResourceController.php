<?php namespace Bonsum\Http\Controllers;

use Illuminate\Http\Request;
use Bonsum\Services\Resource;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class ResourceController extends Controller {


	public function __construct() {

		$this->middleware('admin');
	}


	public function postUpdateResources(Request $request, Resource $resourceService) {

		foreach ($request->all() as $type => $res) {

			if (!in_array($type, Resource::getTypes())) {
				throw new BadRequestHttpException('you need to specify a valid resource type.');
			}
			$resourceService->update($res, $type);
		}
	}

}