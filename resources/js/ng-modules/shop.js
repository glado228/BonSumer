'use strict';

var ngSanitize = require('angular-ng-sanitize');
var infScroll = require('angular-infinite-scroll');
var ngScroll = require('angular-ng-scroll');
var sticky = require('angular-ng-sticky');

module.exports = angular.module('shop', [ngSanitize.name, infScroll.name, ngScroll.name, sticky.name])

.controller('ShopController',
	['$scope', '$http', '$window', '$document', '$location', 'Utils', 'bonsumData',
	function($scope, $http, $window, $document, $location, Utils, bonsumData) {

		$scope.REWARD_TYPE_NO_REWARD = bonsumData.REWARD_TYPE_NO_REWARD;

		$scope.shops = bonsumData.shops || [];
		$scope.totalShops = bonsumData.totalShops || 0;
		$scope.rowSize = 3;
		$scope.shopRows = makeRows($scope.shops, $scope.rowSize);
		$scope.moreItems = $scope.shops.length != bonsumData.totalShops;
		$scope.shopCriteriaMap = bonsumData.shopCriteriaMap;
		$scope.errorFetchingShops = false;


		var selfTriggeredLocationChange = false;
		$scope.$on('$locationChangeSuccess', function(event, current, previous) {

			if (selfTriggeredLocationChange) {
				selfTriggeredLocationChange = false;
			} else {
				initFilter();
				$scope.reloadShops(true);
			}
		});

		function initFilter() {

			var locationFilter = $location.search();
			locationFilter.criteria = angular.fromJson(locationFilter.criteria);
			locationFilter.types = angular.fromJson(locationFilter.types);

			$scope.filter = {
				/*
				* we use initialSearchString to initialize the filter with a normal query string (not hashbanged)
				* This is an ugly hack, ideally we would like to use html5 mode but that causes problems with existing links
 				*
				*/
				searchString: locationFilter.searchString || bonsumData.initialSearchString || '',
				criteria: angular.isArray(locationFilter.criteria) ? locationFilter.criteria : [],
				types: angular.isArray(locationFilter.types) ? locationFilter.types : []
			};
		}

		function updateLocation() {
			var filter = angular.copy($scope.filter);
			if (!filter.searchString) {
				delete filter.searchString;
			}
			if (!$scope.anyTypeSelected()) {
				delete filter.types;
			} else {
				filter.types = angular.toJson(filter.types);
			}
			if (!$scope.anyCriterionSelected()) {
				delete filter.criteria;
			} else {
				filter.criteria = angular.toJson(filter.criteria);
			}
			selfTriggeredLocationChange = true;
			$location.search(filter);
		}

		$scope.reloadShops = function(skipLocationUpdate) {
			$scope.shopRows = []; $scope.shops = [];
			$scope.moreItems = true;
			if (!skipLocationUpdate) {
				updateLocation();
			}
			$scope.loadMoreShops();
			$document.scrollTopAnimated(0);
		};

		$scope.reload = $scope.reloadShops;

		$scope.anyCriterionSelected = function() {
			var res = false;
			angular.forEach($scope.filter.criteria, function(val) {
				res = res || val;
			});
			return res;
		}

		$scope.anyTypeSelected = function() {
			var res = false;
			angular.forEach($scope.filter.types, function(val) {
				res = res || val;
			});
			return res;
		};

		$scope.typeVisible = function(index) {
			return $scope.filter.types[index] || !$scope.anyTypeSelected();
		};

		$scope.loadMoreShops = function() {
			if (!$scope.pendingRequest && $scope.moreItems) {
				$scope.pendingRequest = $http.post(bonsumData.shopFetchUrl,
					{
						index: $scope.shops.length,
						count: 18,
						filter: $scope.filter
					}
				).success(function(data, status) {

					var shops = data.shops;
					$scope.totalShops = data.count;

					if (shops instanceof Array && shops.length > 0) {
						Array.prototype.push.apply($scope.shops, shops);
						$scope.shopRows = makeRows($scope.shops, $scope.rowSize);
					}

					$scope.errorFetchingShops = null;
					$scope.moreItems = $scope.totalShops != $scope.shops.length;

				}).error(function(data, status) {

					if (status == 401 && data == 'TokenMismatch') {
						// token expired, reload page
						$window.location.reload();
					} else {

						$scope.errorFetchingShops = true;
					}

				}).finally(function() {
					$scope.pendingRequest = null;
				});
			}
		};

		function makeRows(shops, rowSize) {

			var shopRows = [];
			var currentRow = [];
			angular.forEach(shops, function(shop, i) {
				if (i % rowSize === 0 && i > 0) {
					shopRows.push(currentRow);
					currentRow = [shop];
				} else {
					currentRow.push(shop);
				}
			});
			if (currentRow.length > 0) {
				shopRows.push(currentRow);
			}

			return shopRows;
		}

		initFilter();
		$scope.reloadShops();

}])
.controller('ShopStubController',
	['$scope', '$modal', '$window', '$document', '$element', 'Utils', 'bonsumData',
	function($scope, $modal, $window, $document, $element, Utils, bonsumData) {


		$scope.shopClicked = function() {

			$document.scrollToElementAnimated($element, 10);
			$modal.open({
				templateUrl: 'shop_forwarding_modal.html',
				size: 'md',
				scope: $scope
			}).result.then(function(no_bonets) {

				if(no_bonets) {
					Utils.redirectTo($scope.shop.link, true);
				}
				else {
					Utils.redirectTo(bonsumData.shopRedirectUrl + '/' + $scope.shop._id, true);
				}
			});
		};

		$scope.setVisibility = function(value) {

			Utils.httpWithDialog(
				bonsumData.shopSetVisibilityUrl + '/' + $scope.shop._id,
				{visible: value},
				function() {
					$window.location.reload();
				},
				function() {
					Utils.openDialogSimple('Error', 'The shop\'s visibility could not be changed');
				},
				null,
				(value ? 'Publishing shop...' : 'Hiding shop...')
			);
		};

		$scope.delete = function() {

			Utils.openDialog('Really delete?', 'The shop with ID ' + $scope.shop._id+ ' (' + $scope.shop.name + ') will be permanently deleted',
				function() {
					Utils.httpWithDialog({
						url: bonsumData.shopDeleteUrl + '/' + $scope.shop._id,
						method: 'delete'
					},
					null,
					function() {
						$window.location.reload();
					},
					function() {
						Utils.openDialogSimple('Error', 'The shop could not be deleted');
					},
					null,
					'Deleting shop...');
			});
		};

	}
]);


