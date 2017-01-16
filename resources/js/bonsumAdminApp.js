'use strict';

require('angular');

var ui_bootstrap = require('angular-ui-bootstrap');
var ng_responsive = require('ng-responsive');

var threeStepsCarousel = require('./ng-modules/threeStepsCarousel');
var editableResource = require('./ng-modules/cms/editableResource');
var articleEditor = require('./ng-modules/cms/articleEditor');
var signUp = require('./ng-modules/signUp');
var AdminUI = require('./ng-modules/admin/adminUI');
var article = require('./ng-modules/article');
var shop = require('./ng-modules/shop');
var animate = require('angular-ng-animate');
var home = require('./ng-modules/home');
var redeem = require('./ng-modules/redeem');
var account = require('./ng-modules/account');
var lexicon = require('./ng-modules/lexicon');
var faq = require('./ng-modules/faq');


angular.module('bonsumApp', [
	ui_bootstrap.name,
	ng_responsive.name,
	threeStepsCarousel.name,
	editableResource.name,
	articleEditor.name,
	signUp.name,
	AdminUI.name,
	article.name,
	home.name,
	shop.name,
	redeem.name,
	animate.name,
	account.name,
	lexicon.name,
	faq.name
])
.value('bonsumData', bonsumData)
.config(['$httpProvider', '$locationProvider', function($httpProvider, $locationProvider) {
	// Angular does not add the X-Requested-With header by default :(
	$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	$locationProvider.html5Mode(false).hashPrefix('!');
}])
.constant('TEXT', {
		SHOW_CHANGES: 'Show modified',
		HIDE_CHANGES: 'Hide modified',
		SHOW_CHANGES_HINT: 'Show/hide modified',
		TURN_EDIT_ON: 'Start editing',
		TURN_EDIT_OFF: 'Stop editing',
		CURRENTLY_EDITING: 'You are currently editing content',
		YOU_HAVE_UNSAVED_CHANGES: 'You have unsaved changes',
		SAVE_CHANGES: 'Save',
		SAVING: 'Saving...',
		DISCARD_CHANGES: 'Discard',
		SAVE_CHANGES_HINT: 'Upload your local changes to the server',
		DISCARD_CHANGES_HINT: 'Discard all your local changes',
		WARNING: 'Warning!',
		EDITOR_TITLE: 'Resource editor',
		REALLY_DISCARD: 'Really discard all your changes?',
		REALLY_NAVIGATE_AWAY: 'You have unsaved change. If you navigate away from this page, they will be lost.',
		PLEASE_ENTER_MEDIA_FILENAME: 'Please enter the path of the media file or leave this field empty'
})
.run(['$interval', '$http', 'bonsumData', function($interval, $http, bonsumData) {

	// send a small request every 5 minutes to keep the session alive
	$interval(function() {
		$http.get(bonsumData.refreshSession);
	}, 300000);

}]);
