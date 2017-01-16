'use strict';

var ngSanitize = require('angular-ng-sanitize');
var infScroll = require('angular-infinite-scroll');
var ngScroll = require('angular-ng-scroll');
var sticky = require('angular-ng-sticky');


module.exports = angular.module('redeem', [ngSanitize.name, infScroll.name, ngScroll.name, sticky.name])

.controller('RedeemController',
	['$scope', '$http', '$window', '$document', '$location', 'Utils', 'bonsumData',
	function($scope, $http, $window, $document, $location, Utils, bonsumData) {

		$scope.REDEEM_TYPE_BONSUMING = bonsumData.REDEEM_TYPE_BONSUMING;
		$scope.REDEEM_TYPE_DONATING = bonsumData.REDEEM_TYPE_DONATING;
		$scope.donateUrl = bonsumData.donateUrl;
		$scope.getVoucherUrl = bonsumData.getVoucherUrl;

		$scope.options = bonsumData.options || [];
		$scope.totalOptions = bonsumData.totalOptions || 0;
		$scope.donationOptionSkip = $scope.shopSkip = 0;
		$scope.rowSize = 3;
		$scope.optionRows = makeRows($scope.options, $scope.rowSize);
		$scope.moreItems = $scope.options.length != bonsumData.totalOptions;
		$scope.shopCriteriaMap = bonsumData.shopCriteriaMap;
		$scope.errorFetchingShops = false;

		var selfTriggeredLocationChange = false;
		$scope.$on('$locationChangeSuccess', function(event, current, previous) {

			if (selfTriggeredLocationChange) {
				selfTriggeredLocationChange = false;
			} else {
				initFilter();
				$scope.reloadOptions(true);
			}
		});

		function initFilter() {

			var locationFilter = $location.search();

			$scope.filter = {
				searchString: locationFilter.searchString || ''
			};
		}

		function updateLocation() {

			var filter = angular.copy($scope.filter);
			if (!filter.searchString) {
				delete filter.searchString;
			}
			selfTriggeredLocationChange = true;
			$location.search(filter);
		}

		$scope.reloadOptions = function(skipLocationUpdate) {

			$scope.optionRows = []; $scope.options = [];
			$scope.shopSkip = $scope.donationOptionSkip = 0;
			$scope.moreItems = true;
			if (!skipLocationUpdate) {
				updateLocation();
			}
			$scope.loadMoreOptions();
			$document.scrollTopAnimated(0);
		};

		$scope.reload = $scope.reloadOptions;

		$scope.anyTypeSelected = function() {
			var res = false;
			angular.forEach($scope.filter, function(val) {
				res = res || val;
			});
			return res;
		};

		$scope.typeVisible = function(index) {
			return $scope.filter.types[index] || !$scope.anyTypeSelected();
		};

		$scope.loadMoreOptions = function() {

			if (!$scope.pendingRequest && $scope.moreItems) {
				$scope.pendingRequest = $http.post(bonsumData.optionsFetchUrl,
					{
						index_shops: $scope.shopSkip,
						index_donation_options: $scope.donationOptionSkip,
						count: 9,
						filter: $scope.filter
					}
				).success(function(data, status) {

					var options = data.options;
					$scope.totalOptions = data.count;
					$scope.shopSkip = data.shopSkip;
					$scope.donationOptionSkip = data.donationOptionSkip;

					if (options instanceof Array && options.length > 0) {
						Array.prototype.push.apply($scope.options, options);
						$scope.optionRows = makeRows($scope.options, $scope.rowSize);
					}

					$scope.errorFetchingOptions = null;
					$scope.moreItems = $scope.totalOptions != $scope.options.length;

				}).error(function(data, status) {

					if (status == 401 && data == 'TokenMismatch') {
						// token expired, reload page
						$window.location.reload();
					} else {

						$scope.errorFetchingOptions = true;
					}

				}).finally(function() {
					$scope.pendingRequest = null;
				});
			}
		};

		function makeRows(options, rowSize) {

			var optionRows = [];
			var currentRow = [];
			angular.forEach(options, function(option, i) {
				if (i % rowSize === 0 && i > 0) {
					optionRows.push(currentRow);
					currentRow = [option];
				} else {
					currentRow.push(option);
				}
			});
			if (currentRow.length > 0) {
				optionRows.push(currentRow);
			}
			return optionRows;
		}

		initFilter();
		$scope.reloadOptions();

}])
.controller('OptionStubController',
	['$scope', '$window', '$document', '$element', '$modal', '$rootScope', '$http', 'Utils', 'Resources', 'bonsumData',
	function($scope, $window, $document, $element, $modal, $rootScope, $http, Utils, Resources, bonsumData) {

		$scope.redeemClicked = function() {

			$scope.currentUser = bonsumData.currentUser;
			$document.scrollToElementAnimated($element, 10);
			$scope.redeemModal = $modal.open({
				templateUrl:'redeem_modal.html',
				size: 'md',
				scope: $scope
			});
		};

		$scope.donate = function(bonets, success_message) {

			$scope.redeemModal.close();
			Utils.httpWithDialog(
				bonsumData.donateUrl + '/' + $scope.option._id,
				{ bonets: bonets },
				function(response) {
					$scope.header_message = Resources.getText('redeem.donation_subject');
					$scope.success_message = success_message;
					$modal.open({
						templateUrl: 'redeem_success_modal.html',
						size: 'md',
						scope: $scope
					}).result.finally(
						function () {
							$scope.reloadOptions();
							Utils.reloadUser();
						}
					);
				},
				function(response) {
					Utils.openDialogSimple(Resources.getText('general.error_occurred'), Resources.getText('general.please_try_again'));
				}
			);
		};

		$scope.getVoucher = function(amount, bonets_value, success_message) {

			$scope.redeemModal.close();
			Utils.httpWithDialog(
				bonsumData.getVoucherUrl + '/' + $scope.option._id,
				{ amount: amount},
				function(response) {
					$scope.voucher = response.data;
					$scope.header_message = Resources.getText('redeem.voucher_subject');
					$scope.success_message = success_message;
					$scope.success_coda = Resources.getText('redeem.voucher_success_coda');
					$modal.open({
						templateUrl: 'redeem_success_modal.html',
						size: 'md',
						scope: $scope
					}).result.finally(
						function () {
							$scope.reloadOptions();
							Utils.reloadUser();
						}
					);
				},
				function(response) {
					Utils.openDialogSimple(Resources.getText('general.error_occurred'), Resources.getText('general.please_try_again'));
				}
			);
		};

		$scope.setVisibility = function(value) {

			Utils.httpWithDialog(
				bonsumData.optionSetVisibilityUrl + '/' + $scope.option._id,
				{visible: value},
				function() {
					$window.location.reload();
				},
				function() {
					Utils.openDialogSimple('Error', 'The donation option\'s visibility could not be changed');
				},
				null,
				(value ? 'Publishing donation option...' : 'Hiding donation option...')
			);
		};

		$scope.delete = function() {

			Utils.openDialog('Really delete?', 'The donation option with ID ' + $scope.option._id + ' (' + ($scope.option.name || '<NO NAME>') + ') will be permanently deleted',
				function() {
					Utils.httpWithDialog({
						url: bonsumData.optionDeleteUrl + '/' + $scope.option._id,
						method: 'delete'
					},
					null,
					function() {
						$window.location.reload();
					},
					function() {
						Utils.openDialogSimple('Error', 'The donation option could not be deleted');
					},
					null,
					'Deleting donation option...');
			});
		};

	}
]);


