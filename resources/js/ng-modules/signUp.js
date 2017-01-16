'use strict';

var utils = require('./utils');

module.exports = angular.module('signUp', [utils.name])
.controller('SignUpFormController', ['$scope', '$window', '$controller', 'bonsumData', function($scope, $window, $controller, bonsumData) {

    angular.extend(this, $controller('FormBaseController', {$scope: $scope}));

    if (bonsumData.currentUser && bonsumData.currentUser.admin) {
	    $scope.formdata.terms_and_conditions = true;
	}

    $scope.FormBaseController = {
		serviceUrl: bonsumData.signupUrl,
		savingMessage: bonsumData.SIGNING_UP_MESSAGE,
		success: function(response) {
			$window.location.href = response.data;
		}
	};

 }]);
