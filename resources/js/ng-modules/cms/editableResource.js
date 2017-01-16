'use strict';


var resourceEditor = require('./resourceEditor');
var adminToolBar = require('../admin/adminToolBar');

module.exports = angular.module('cms.editableResource', [adminToolBar.name, resourceEditor.name])
.controller('EditableResourceController', [
	'$scope',
	'$window',
	'$timeout',
	'$anchorScroll',
	'$location',
	'ResourceEditorState',
	'EditingState',
	'Resources',
	'bonsumData',
	'TEXT',
	'RESOURCE_TYPES',
	function($scope, $window, $timeout, $anchorScroll, $location, ResourceEditorState, EditingState, Resources, bonsumData, TEXT, RESOURCE_TYPES) {

		$scope.ResourceEditorState = ResourceEditorState;

		$scope.init = function(res_id, res_type) {

			$scope.res_id = res_id;
			$scope.res_type = parseInt(res_type);
			$scope.resources = Resources.getAll()[res_type];

			// generate a unique id for this editor
			$scope.editor_id = res_type + res_id;
			for (var i = 0; i < 10; ++i) {
				$scope.editor_id += Math.floor(Math.random()*10).toString();
			}
		};

		$scope.$on('discardChanges', function() {

        	var res_id = $scope.res_id;
			var res_type = $scope.res_type;
         	Resources.set(res_id, res_type, bonsumData.resources[res_type][res_id]);
       	});


		$scope.editing = function() {

			return EditingState.editing;
		};

		$scope.hasChanges = function() {

			var res_id = $scope.res_id;
			var res_type = $scope.res_type;
			return Resources.get(res_id, res_type) !== bonsumData.resources[res_type][res_id];
		};


		$scope.thisEditorOpen = function() {

			return ResourceEditorState.getTargetId() === $scope.editor_id;
		};

		$scope.changesShowing = function() {

			return EditingState.changesShowing;
		};

		$scope.resourceClicked = function(event) {

			if (!$scope.editing() || event.shiftKey) {
				return;
			}

			event.stopPropagation();
			event.preventDefault();

			if ($scope.res_type === RESOURCE_TYPES.TEXT) {

				if (!$scope.thisEditorOpen()) {

					ResourceEditorState.closeEditor();
					ResourceEditorState.openEditor($scope.res_id, $scope.editor_id, event.currentTarget);
				}

			} else {
				// resource type is not text
				ResourceEditorState.closeEditor();

				$timeout(function() {
					var user_input = prompt(TEXT.PLEASE_ENTER_MEDIA_FILENAME, Resources.get($scope.res_id, $scope.res_type));
					if (user_input) {
						Resources.set($scope.res_id, $scope.res_type, user_input);
					} else if (user_input === '') {
						Resources.set($scope.res_id, $scope.res_type, null);
					}
				});
			}
		};
	}
]);


