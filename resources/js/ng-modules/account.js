'use strict';

var ngSanitize = require('angular-ng-sanitize');
var ngRoute = require('angular-ng-route');
var infScroll = require('angular-infinite-scroll');
var sticky = require('angular-ng-sticky');

module.exports = angular.module('Account', [ngRoute.name, infScroll.name, ngSanitize.name, sticky.name])
.controller('AccountSideBarController',
	[ '$scope', '$location', '$modal', 'Utils', 'Resources',
	function($scope, $location, $modal, Utils, Resources) {

		$scope.history_open = true;
		$scope.personal_data_open = true;

		$scope.isActive = function(url) {
			return $location.url() === url;
		};

		$scope.logout = Utils.logout;

}])
.controller('HistoryController', [
	'$scope',
	'$http',
	'$window',
	'$routeParams',
	'bonsumData',
	function($scope, $http, $window, $routeParams, bonsumData) {
		$scope.fetchHistoryURL = bonsumData.fetchHistoryURL;
		var type = $routeParams['type'];

		$scope.items = [];
		$scope.overview = {};
		$scope.moreItems = true;
		$scope.errorFetchingItems = false;

		$scope.filter = {
			type: type
		};

		$scope.loadMoreItems = function() {

			if (!$scope.pendingRequest && $scope.moreItems) {

				$scope.pendingRequest = $http.post($scope.fetchHistoryURL,
					{
						filter: $scope.filter,
						index: $scope.items.length,
						count: 25
					}
					).success(function(data, success) {
						$scope.totalItems = data.count;
						Array.prototype.push.apply($scope.items, data.items);
						if (data.overview) {
							$scope.overview = data.overview;
						}

						$scope.errorFetchingItems = false;
						$scope.moreItems = $scope.items.length != $scope.totalItems;
					})
					.error(function(data, status) {

						if (status == 401 && data == 'TokenMismatch') {
							// token expired, reload page
							$window.location.reload();
						} else {
							$scope.errorFetchingItems = true;
						}
					})
					.finally(function() {
						$scope.pendingRequest = null;
					});
			}
		};

		$scope.loadMoreItems();

}])
.controller('PersonalDataController', [
	'$scope',
	'$controller',
	'bonsumData',
	'Resources',
	'Utils',
	'$location',
	function($scope, $controller, bonsumData, Resources, Utils, $location) {

		angular.extend(this, $controller('FormBaseController', {$scope: $scope}));

		function initForm() {
			$scope.formdata = angular.copy(bonsumData.user);
			$scope.formdata.gender = $scope.formdata.gender || '';
		}
		var updateURL = $location.url() === "/password" ? bonsumData.updatePasswordURL : bonsumData.updateInfoURL;

		$scope.FormBaseController = {
			serviceUrl: updateURL,
			success: function(response) {
				bonsumData.user = response.data.user;
				initForm();
				Utils.openDialogSimple(Resources.getText('account.information_updated'));
			},
			error: function(response) {
				$scope.errors = response.data;
				Utils.openDialogSimple(Resources.getText('general.error_occurred'), Resources.getText('general.please_try_again'));
			}
		};

		initForm();
    }
])
.controller('ReferFriendsController', [
        '$scope',
        '$controller',
        'bonsumData',
        'Resources',
        'Utils',
        function($scope, $controller, bonsumData, Resources, Utils) {

            angular.extend(this, $controller('FormBaseController', {$scope: $scope}));

            function initForm() {
	            $scope.formdata.message = Resources.getText('account.refer_friends_msg_content');
				$scope.formdata.email = '';
            }

            $scope.FormBaseController = {
                serviceUrl: bonsumData.sendInviteEmailURL,
                success: function() {
                    Utils.openDialogSimple(Resources.getText('account.invitation_sent'));
                    initForm();
                },
                error: function(response) {
                    $scope.errors = response.data;
                    Utils.openDialogSimple(Resources.getText('general.error_occurred'), Resources.getText('general.please_try_again'));
                }
            };

            initForm();
        }
])
.config(['$routeProvider', '$locationProvider',
	function($routeProvider, $locationProvider) {

		$routeProvider.when('/history/:type', {
			'templateUrl': function(routeParams) {
				return routeParams['type'] + '.html';
			},
			'controller': 'HistoryController',
		})
		.when('/personal', {
			'templateUrl': 'personal_data.html',
			'controller': 'PersonalDataController'
		})
		.when('/password', {
			'templateUrl': 'change_password.html',
			'controller': 'PersonalDataController'
		})
		.when('/refer_friends', {
			'templateUrl': 'refer_friends.html',
			'controller': 'ReferFriendsController'
		})
		.otherwise({
			'redirectTo': '/history/vouchers',
		});

}])
