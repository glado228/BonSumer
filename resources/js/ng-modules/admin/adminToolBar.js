'use strict';

var resourceEditor = require('../cms/resourceEditor');
var editingState = require('../cms/editingState');
var utils = require('../utils');

module.exports = angular.module('admin.toolbar', [editingState.name, utils.name, resourceEditor.name])
.controller('AdminToolBarController', [
	'$scope',
	'$rootScope',
	'$http',
	'$modal',
	'$q',
	'$window',
	'$filter',
	'TEXT',
	'ResourceEditorState',
	'EditingState',
	'Resources',
	'Utils',
	'bonsumData',
	function($scope, $rootScope, $http, $modal, $q, $window, $filter, TEXT, ResourceEditorState, EditingState, Resources, Utils, bonsumData) {

	$scope.TEXT = TEXT;
	$scope.selectedLocale = bonsumData.locale;
	$scope.saving = false;


	$scope.editorOpen = function() {

		return ResourceEditorState.isOpen();
	};

	$scope.discardChanges = function(event) {

		event.stopPropagation();

		Utils.openDialog(TEXT.WARNING, TEXT.REALLY_DISCARD,
			function() {
				$rootScope.$broadcast('discardChanges');
				EditingState.toggleChanges(false);
			}
		);
	};

	$scope.commitChanges = function() {

		var resources = Resources.getAll();
		for (var type in resources) {
			for (var res in resources[type]) {
				bonsumData.resources[type][res] = resources[type][res];
			}
		}
	};

	$scope.saveChanges = function(event) {

		if (event) {
			event.stopPropagation();
		}
		Utils.httpWithDialog(
			bonsumData.updateResourceUrl,
			$scope.getChanges(),
			$scope.commitChanges,
			function(response) {
				var msg = 'Error while saving resources';
				if (response.data && typeof(response.data) === 'string') {
					msg += ': ' + response.data;
				}
				if (response.status === 401) {
					if (response.data === 'TokenMismatch') {
						// if only the token has expired, we issue a get request to refresh it and repeat the operation
						$http.get(bonsumData.refreshSession)
						.success(function() {
							$scope.saveChanges();
						})
						.error(function() {
							Utils.openDialogSimple('Error', msg);
						});
					} else {
						Utils.openDialogSimple('Error', 'Your session has expired, and you will need to login again. Your changes will be lost.', redirectToLogin);
					}
				} else {
					Utils.openDialogSimple('Error', msg);
				}
			},
			function() {
				$scope.saving = false
			},
			'Saving resources...',
			function() {
				$scope.saving = true;
			}
		);
	};

	$scope.editing = function() {

		return EditingState.editing;
	};

	$scope.toggleEditing = function(event) {

		event.stopPropagation();
		EditingState.toggleChanges(false);
		EditingState.toggleEditing();
		ResourceEditorState.closeEditor();
	};

	$scope.changesShowing = function() {

		return EditingState.changesShowing;
	};

	$scope.toggleChanges = function(event) {

		event.stopPropagation();
		EditingState.toggleChanges();
		ResourceEditorState.closeEditor();
	};

	$scope.howManyChanged = function() {

		var cnt = 0;
		var changes = $scope.getChanges();
		for (var type in changes) {
			for (var res in changes[type]) {
				cnt++;
			}
		}
		return cnt;
	};

	$scope.getChanges = function() {

		var changes = {};
		var resources = Resources.getAll();
		for (var type in resources) {
			for (var res in resources[type]) {
				if (resources[type][res] !== bonsumData.resources[type][res]) {
					if (!changes[type]) {
						changes[type] = {};
					}
					changes[type][res] = resources[type][res];
				}
			}
		}
		return changes;
	};

	Utils.registerBeforeUnload(function() {
		return ($scope.howManyChanged() || ResourceEditorState.isOpen());
	});

	$scope.localeChanged = function() {

		function doChange() {
			$window.onbeforeunload = null;
			$window.location.search = ('setLocale=' + $scope.selectedLocale);
		}

		if ($scope.howManyChanged()) {
			Utils.openDialog(TEXT.WARNING, TEXT.REALLY_NAVIGATE_AWAY, doChange, function() {
				$scope.selectedLocale = bonsumData.locale;
			});
		} else {
			doChange();
		}

	};

	function redirectToLogin() {

		Utils.redirectTo(bonsumData.loginUrl);
	}

}])
.constant('TOOLBAR', {
	// height of admin toolbar
	HEIGHT: 70
}).run(['$anchorScroll', 'TOOLBAR', function($anchorScroll, TOOLBAR) {

	$anchorScroll.yOffset = TOOLBAR.HEIGHT;
}]);
