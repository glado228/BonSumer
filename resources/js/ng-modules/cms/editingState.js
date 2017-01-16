'use strict';


module.exports = angular.module('cms.editingState', [])
.service('EditingState', function() {

	this.changesShowing = false,
	this.editing = false,

	this.toggleEditing = function(state) {
		if (state === undefined) {
			this.editing = !this.editing;
		} else {
			this.editing = state;
		}
	};

	this.toggleChanges = function(state) {
		if (state === undefined) {
			this.changesShowing = !this.changesShowing;
		} else {
			this.changesShowing = state;
		}
	};
});
