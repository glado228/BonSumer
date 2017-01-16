'use strict';

require('angular');
var ui_bootstrap = require('angular-ui-bootstrap');
var ng_responsive = require('ng-responsive');
var ThreeStepsCarousel = require('./ng-modules/threeStepsCarousel');
var signUp = require('./ng-modules/signUp');
var article = require('./ng-modules/article');
var shop = require('./ng-modules/shop');
var animate = require('angular-ng-animate');
var lexicon = require('./ng-modules/lexicon');
var home = require('./ng-modules/home');
var redeem = require('./ng-modules/redeem');
var account = require('./ng-modules/account');
var faq = require('./ng-modules/faq');

angular.module('bonsumApp', [
	ui_bootstrap.name,
	ng_responsive.name,
	ThreeStepsCarousel.name,
	signUp.name,
	article.name,
	lexicon.name,
	home.name,
	shop.name,
	redeem.name,
	animate.name,
	account.name,
	faq.name
])
.value('bonsumData', bonsumData)
.config(['$httpProvider', '$locationProvider', function($httpProvider, $locationProvider) {
	// Angular does not add the X-Requested-With header by default :(
	$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	$locationProvider.html5Mode(false).hashPrefix('!');
}])
.run(['$interval', '$http', 'bonsumData', function($interval, $http, bonsumData) {

	// send a small request every 5 minutes to keep the session alive
	$interval(function() {
		$http.get(bonsumData.refreshSession);
	}, 300000);

}]);
