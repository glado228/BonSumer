'use strict';

var infScroll = require('angular-infinite-scroll');
var sticky = require('angular-ng-sticky');
var ui_utils = require('ng-ui-utils');

module.exports = angular.module('article', [infScroll.name, sticky.name, ui_utils.scroll_jqlite.name])
.controller('ArticleController', [
	'$scope',
	'$http',
	'$document',
	'bonsumData',
	function($scope, $http, $document, bonsumData) {

		$scope.articles = bonsumData.articles || [];
		$scope.totalArticles = bonsumData.totalArticles;
		$scope.articleColumns= makeColumns($scope.articles);
		$scope.moreItems = $scope.articles.length != bonsumData.totalArticles;
		$scope.errorFetchingArticles = false;

		/* fitler parameters */

		$scope.sortingOptions = bonsumData.sortingOptions;

		$scope.filter = {
			searchString: bonsumData.currentFilter.searchString || '',
			sorting: bonsumData.currentFilter.sorting || bonsumData.sortingOptions[0].value,
		};


		$scope.reloadArticles = function() {
			$scope.articleColumns= [[],[]]; $scope.articles = [];
			$scope.moreItems = true;
			$scope.loadMoreArticles();

			$document.scrollTopAnimated(0);
		};

		$scope.loadMoreArticles = function() {

			if (!$scope.pendingRequest && $scope.moreItems) {
				$scope.pendingRequest = $http.post(bonsumData.articleFetchUrl,
					{
						index: $scope.articles.length,
						count: 9,
						filter: $scope.filter
					}
				).success(function(data, status) {

					var articles = data.articles;
					$scope.totalArticles = data.count;

					if (articles instanceof Array && articles.length > 0) {
						Array.prototype.push.apply($scope.articles, articles);
						$scope.articleColumns= makeColumns($scope.articles);
					}

					$scope.errorFetchingArticles = null;
					$scope.moreItems = $scope.totalArticles != $scope.articles.length;

				}).error(function(data, status) {

					if (status == 401 && data == 'TokenMismatch') {
						// token expired, reload page
						$window.location.reload();
					} else {

						$scope.errorFetchingArticles = true;
					}

				}).finally(function() {
					$scope.pendingRequest = null;
				});
			}
		};

		function makeColumns(articles) {

			var articleColumns= [[],[]];

			angular.forEach(articles, function(article, i) {
				articleColumns[i % 2].push(article);
			});

			return articleColumns;
		}
	}
]);
