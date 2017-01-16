'use strict';

module.exports = angular.module('ThreeStepsCarousel', [])
.controller('ThreeStepsCarouselCtrl', [
	'$scope',
	function($scope) {

		$scope.slides = {
			'shopSustainably': {
				'active': true
			},
			'collectBonets': {
				'active': false
			},
			'redeemBonets': {
				'active': false
			}
		};

		$scope.changeInterval = 4000;
	}
]);
