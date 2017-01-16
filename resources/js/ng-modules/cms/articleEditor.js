'use strict';

var utils = require('../utils');
var ng_tagsinput = require('angular-ng-tagsinput');

module.exports = angular.module('cms.articleEditor', [utils.name, ng_tagsinput.name])
.controller('ArticleFormController', [
	'$scope',
	'$timeout',
	'$controller',
	'Utils',
	'bonsumData',

	function($scope, $timeout, $controller, Utils, bonsumData) {

    angular.extend(this, $controller('FormBaseController', {$scope: $scope}));

    $scope.Utils = Utils;

    Utils.registerBeforeUnload(function() {
    	return $scope.form.$dirty;
    });

	$scope.formdata = (bonsumData.article ? bonsumData.article : {});
	$scope.formdata.date = ($scope.formdata.date_string ? new Date($scope.formdata.date_string) : new Date());
	if (!$scope.formdata.body) {
		$scope.formdata.body = "";
	}
	$scope.formdata.visible = Boolean($scope.formdata.visible);

	$scope.FormBaseController = {
		serviceUrl: {
			url: (bonsumData.article ? bonsumData.articleUpdateUrl + '/' + (bonsumData.article.id || bonsumData.article._id) : bonsumData.articleStoreUrl),
			method: (bonsumData.article ? 'put' : 'post')
		},
		savingMessage: 'Saving article',
		preprocessForm: function(formdata, validation) {
			var article = angular.copy(formdata);
			if (!validation) {
				// do not send the entire article body to the server if are just validating
				article.body = CKEDITOR.instances.body.getData();
			}
			article.date = (article.date instanceof Date ? article.date.toDateString() : '');
			return article;
		},
		success: function(response) {
			Utils.redirectTo(bonsumData.backUrl);
		},
		error: function(response) {
			Utils.openDialogSimple('Operation failed', 'Please check the form for errors.');
		}
	};

	$scope.delete = function() {

		if (bonsumData.article) {

			Utils.openDialog('Really delete?', 'The article with ID ' + (bonsumData.article.id || bonsumData.article._id) + ' will be permanently deleted',
				function() {
					Utils.httpWithDialog({
						url: bonsumData.articleDeleteUrl + '/' + (bonsumData.article.id || bonsumData.article._id),
						method: 'delete'
					},
					null,
					function() {
						Utils.redirectTo(bonsumData.backUrl);
					},
					function() {
						Utils.openDialogSimple('Error', 'The article could not be deleted');
					},
					null,
					'Deleting article...');
			});
		}
	};

	$scope.changeImage = function() {

		$timeout(function() {
			var user_input = prompt('Please enter a location for the image. You can either enter an external resource, like "http://www.myimages.com/apples.jpg", or a resource in our media library. The latter have to start with "/", like in "/home/apples.jpg".',
				$scope.formdata.image);
			$scope.formdata.image = user_input;
			$scope.form.$setDirty();
		});
	};

	$scope.changeThumbnail = function() {

		$timeout(function() {
			var user_input = prompt('Please enter a location for the thumbnail. You can either enter an external resource, like "http://www.myimages.com/apples.jpg", or a resource in our media library. The latter have to start with "/", like in "/home/apples.jpg".',
				$scope.formdata.thumbnail);
			$scope.formdata.thumbnail = user_input;
			$scope.form.$setDirty();
		});
	};

	CKEDITOR.instances.body.on('change', function() {

		$scope.form.$setDirty();
	});

}])
.controller('ArticleStubController', ['$scope', '$window', 'Utils', 'bonsumData', function($scope, $window, Utils, bonsumData) {


	$scope.setVisibility = function(value) {

		Utils.httpWithDialog(
			bonsumData.articleSetVisibilityUrl + '/' + $scope.article._id,
			{visible: value},
			function() {
				$window.location.reload();
			},
			function() {
				Utils.openDialogSimple('Error', 'The article\'s visibility could not be changed');
			},
			null,
			(value ? 'Publishing article...' : 'Hiding article...')
		);
	};

	$scope.delete = function() {

		Utils.openDialog('Really delete?', 'The article with ID ' + $scope.article._id + ' (' + $scope.article.title + ') will be permanently deleted',
			function() {
				Utils.httpWithDialog({
					url: bonsumData.articleDeleteUrl + '/' + $scope.article._id,
					method: 'delete'
				},
				null,
				function() {
					$window.location.reload();
				},
				function() {
					Utils.openDialogSimple('Error', 'The article could not be deleted');
				},
				null,
				'Deleting article...');
		});
	};

}]);
