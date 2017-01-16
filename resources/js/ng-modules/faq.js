'use strict';


module.exports = angular.module('Faq', [])
.controller('FaqController', ['$scope',
	function($scope) {

		$scope.toggle = function(opened) {
			for (var i = 0; i < totalGroups; ++i) {
				$scope.status.open[i] = opened;
			}
		};

		var totalGroups = 12;

		$scope.status = {
			open: []
		};
		$scope.toggle(false);

	}
]);

