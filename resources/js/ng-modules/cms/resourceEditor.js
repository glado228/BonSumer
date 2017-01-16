'use strict';

var editingState = require('./editingState');

module.exports = angular.module('cms.resourceEditor', [editingState.name])
.service('ResourceEditorState', ['$timeout', '$rootScope', '$document', 'bonsumData', 'Resources', 'EditingState', 'RESOURCE_TYPES', 'TOOLBAR',
	function($timeout, $rootScope, $document, bonsumData, Resources, EditingState, RESOURCE_TYPES, TOOLBAR) {

	// ID of current target, null otherwise
	this.targetId = null;
	// ID and type of resource being edited
	this.resId = null;


	this.isOpen = function() {
		return (this.targetId !== null);
	};

	this.getTargetId = function() {
		return this.targetId;
	};

	this.closeEditor = function() {
		if (this.targetId) {
			var ck = CKEDITOR.instances[this.targetId];
			var text = ck.getData();
			if (text === '') {
				// empty text means delete resource, we replace it with null because
				// this is what the backend expects
				text = null;
			}
			Resources.setText(this.resId, text);
			ck.destroy();
		}
		this.targetId = null;
	};

	this.openEditor = function(resId, editorId, targetElement) {

		EditingState.toggleChanges(false);

		this.resId = resId;
		this.targetId = editorId;
		this.targetElement = targetElement;

		CKEDITOR.inline(editorId);
		var ck = CKEDITOR.instances[editorId];
		ck.config.enterMode = CKEDITOR.ENTER_BR; // to remove atomatically wrapping of content
		ck.config.language = bonsumData.language;
		// in <p> tags
	}

}]);
