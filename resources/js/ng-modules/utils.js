'use strict';


module.exports = angular.module('Utils', [])
.service('Utils', ['$rootScope', '$window', '$modal', '$http', '$q', '$timeout', '$log', 'Resources', 'bonsumData', function($rootScope, $window, $modal, $http, $q, $timeout, $log, Resources, bonsumData) {

	this._beforeunloadCallbacks = [];
	this.registerBeforeUnload = function(callback) {
		this._beforeunloadCallbacks.push(callback);
	};

	var self = this;
	$window.onbeforeunload = function() {

		var warning = false;
		angular.forEach(self._beforeunloadCallbacks, function(callback) {
			if (!warning && typeof(callback) === 'function' && callback()) {
				warning = true;
			}
		});
		if (warning) {
			return Resources.getText('general.really_navigate_away');
		}
	};

	this.logout = function() {

		$http.post(bonsumData.logoutUrl, {})
		.success(function(data) {
			self.redirectTo(data);
		})
		.error(function() {
			$window.location.reload();
		});
	};

	/**
	 * Redirect to a URL if it is valid, otherwise raise an error
	 * @param  string url
	 */
	this.redirectTo = function(url, new_window) {

		if ((url instanceof String || typeof(url) === 'string')
			&& (url.substr(0,7) === 'http://' || url.substr(0,8) === 'https://')) {

			if (new_window) {
				$window.open(url);
			} else {
				$window.onbeforeunload = null;
				$timeout(function() {
					$window.location.href = url;
				});
			}
		} else {
			$log.error('Error: requested to redirect to malformed URL ' + url + ' ' + url.substr(0,7));
			this.openDialogSimple('Error: requested to redirect to malformed URL');
		}
	};

	this.openDialogSimple = function(title, message, ok_action, ok_label) {

		return this.openDialog(title, message, ok_action, 'no', ok_label);
	};

	this.openDialog = function(title, message, ok_action, cancel_action, ok_label, cancel_label) {

		var scope = $rootScope.$new();
		scope.title = title || Resources.getText('general.warning')
		scope.message = message;
		scope.ok_label = ok_label || Resources.getText('general.ok');
		scope.cancel_label = cancel_label || Resources.getText('general.cancel');
		if (cancel_action === 'no') {
			cancel_action = null;
			scope.hide_cancel = true;
		}

		var m = $modal.open({
			templateUrl: 'modal_dialog.html',
			backdrop: 'static',
			scope: scope
		});
		m.result.then(ok_action, cancel_action);
		return m;
	};

	this.showProgress = function(title, cancel_action, cancel_label) {

		var scope = $rootScope.$new();
		scope.title = title || Resources.getText('general.please_wait');
		scope.cancel_label = cancel_label || Resources.getText('general.cancel');

		var m = $modal.open({
			templateUrl: 'progress_dialog.html',
			backdrop: 'static',
			keyboard: false,
			scope: scope,
			size: 'sm'
		});
		m.result.catch(cancel_action);
		return m;
	};

	/**
	 * HTTP request with progress dialog
	 *
	 * url can be either a string (post method will be assumed) or an object with keys "url" and "method"
	 */
	this.httpWithDialog = function(url, data, success, error, final, dialogTitle, before) {

		var method = 'post';
		if (url instanceof Object) {
			method = url.method;
			url = url.url;
		}

		if (before) {
			before();
		}

		var progress;
		var canceler = $q.defer();

		var self = this;
		var timeout = $timeout(function() {
			progress = self.showProgress(dialogTitle, canceler.resolve);
		}, 1000);

		return $http({
			url: url,
			data: data,
			method: method,
			timeout: canceler.promise
		}).then(function() {
			if (success) {
				success.apply(this, arguments);
			}
		}, function() {
			if (error) {
				error.apply(this, arguments);
			}
		})
		.finally(function() {
			$timeout.cancel(timeout);
			if (progress) {
				progress.dismiss();
			}
			if (final) {
				final.apply(this, arguments);
			}
		});
	};


	/**
	 * Create a usable image link so we can display a preview
	 * @return the image URL
	 */
	this.makeImageLink = function(img_loc) {
		if (img_loc) {
			if (img_loc.substr(0,1) === '/') {
				if (img_loc.substr(0,7) !== '/media/') {
					return bonsumData.imagePath + img_loc;
				}
				return img_loc
			}
			if (img_loc.substr(0,7) !== 'http://') {
				 return 'http://' + img_loc;
			}
		}
		return 'http://';
	};

	/*
		reload the user information and stores it in bonsumData.currentUser
	 */
	this.reloadUser = function() {

		$http.get(bonsumData.getUserUrl)
		.success(function(data) {
			bonsumData.currentUser = data;
		})
	};

}])
.controller('FormBaseController', ['$scope', '$http', 'Utils', function($scope, $http, Utils) {
	/*
		base controller for forms.

		the form model is in: $scope.formdata

		call changed() to trigger server-side asynchronous validation. Errors are stored in $scope.errors

		Configurable options and callbacks:

		$scope.FormBaseController = {
			serviceUrl
			savingMessage
			keepSaving (if true, keep the isSaving flag true even after submitting)
			preprocessForm (function, called with arguments (formdata, validation) where validation is true if we are just
							sending the form for server-side validation and not trying to submit
			error (function) (called after setting the errors in $scope.erros, which is done aynway)
			success (function)
			before (function)
			final (function)
			changed (function) (called whenever the form changes. If it returns true, validation is not performed)
		}
	 */

	$scope.formdata = {};
	$scope.FormBaseController = {};

	$scope.submitFailed = function() {

		return $scope.form.$submitted && $scope.errors;
	};

	$scope.showErrorFor = function() {

		var res = false;
		angular.forEach(arguments, function(field) {
			if (!res) {
				if (($scope.form.$submitted
					|| ($scope.form.hasOwnProperty(field) && ($scope.form[field].$dirty || $scope.form[field].$touched)))
					&& $scope.errors && $scope.errors[field]) {
					res = true;
				}
			}
		});
		return res;
	};

	$scope.changed = function() {

		$scope.form.$setDirty();

		if (($scope.FormBaseController.changed || angular.noop).apply(this, arguments)) {
			return;
		}

		var url = $scope.FormBaseController.serviceUrl;
		var method = 'post';
		if (url instanceof Object) {
			method = url.method;
			url = url.url;
		}

		$http({
			url: url,
			method: method,
			timeout: 5000,
			data: angular.extend({}, ($scope.FormBaseController.preprocessForm || angular.identity)($scope.formdata, true), {'validate': true})
		})
		.then(
			function(response) {
				$scope.errors = null;
			},
			function(response) {
				$scope.errors = response.data;
			}
		);
	};

	$scope.submit = function(event) {

		event.preventDefault();
		// to make sure the "showErrorFor" can tell whether the form has already been submitted once
		$scope.form.$setSubmitted();

		Utils.httpWithDialog($scope.FormBaseController.serviceUrl,
			($scope.FormBaseController.preprocessForm || angular.identity)($scope.formdata, false)
			,
			function() {
				$scope.errors = null;
				($scope.FormBaseController.success || angular.noop).apply(this, arguments);
			},
			function(response) {
				$scope.errors = response.data;
				($scope.FormBaseController.error || angular.noop).apply(this, arguments);
			},
			function() {
				$scope.isSaving = ($scope.FormBaseController.keepSaving || false);
				($scope.FormBaseController.final || angular.noop).apply(this, arguments);
			},
			$scope.FormBaseController.savingMessage,
			function() {
				$scope.isSaving = true;
				($scope.FormBaseController.before || angular.noop).apply(this, arguments);
			}
		);
	};
}])
.constant('RESOURCE_TYPES', {
		TEXT: bonsumData.resourceTypes.text,
		IMG: bonsumData.resourceTypes.img
})
.service('Resources', ['bonsumData', 'RESOURCE_TYPES', function(bonsumData, RESOURCE_TYPES) {

	this._res = {}
	for (var type in bonsumData.resources) {
		this._res[type] = {};
		for (var res in bonsumData.resources[type]) {
			this._res[type][res] = bonsumData.resources[type][res];
		}
	}

	this.getAll = function() {
		return this._res;
	};

	this.setText = function(key, value) {
		return this.set(key, RESOURCE_TYPES.TEXT, value);
	};

	this.setImage = function(key, value) {
		return this.set(key, RESOURCE_TYPES.IMAGE, value);
	};

	this.getText = function(key) {
		return this.get(key, RESOURCE_TYPES.TEXT);
	};

	this.getImage = function(key) {
		return this.get(key, RESOURCE_TYPES.IMG);
	};

	this.set = function(key, type, value) {
		this._res[type][key] = value;
		return value;
	};

	this.get = function(key, type) {
		if (this._res[type].hasOwnProperty(key)) {
			return this._res[type][key];
		}
		return null;
	};

}])
.controller('DatePickerController', ['$scope', function($scope) {

	$scope.open = function(event) {
		event.stopPropagation();
		event.preventDefault();
		$scope.opened = true;
	};
}])
.directive('backImg', function() {

	return function(scope, element, attrs) {
		var url = attrs.backImg;
        element.css({
            'background-image': 'url(' + url +')',
            'background-repeat' : 'no-repeat',
            'background-position': '50% 50%'
        });
	}
})
.service('Capabilities', function() {
	// This service is for detecting capabilities of the environment we run in.
	// For now, this means whether it's a touch screen or not.

	this.supports = {
		touchEvents: false
	};

	this.detect =  function() {
		this._detectTouch();
	};

	this._detectTouch = function () {
		//there are many ways to detect touch support, but this seems to
		//be the more reliable
		this.supports.touchEvents = 'ontouchstart' in window || !!(navigator.msMaxTouchPoints);
	};

	//run detection
	this.detect();
})
.directive('scrollToTop', function() {
	//simple directive that scrolls (animated) the view to the top

	var link = function(scope, element, attrs) {
		var timer = -1,
				interval = 15, //msecs between consecutive scrolls
				speed = attrs.speed || 600, //time the whole animation takes
				hide = attrs.hide;

		var scrollToTop = function() {
			var number_of_steps = speed/interval,
					step = window.scrollY / number_of_steps;

			// just in case, to avoid stray timers, we clear before creating a new one
			clearInterval(timer);

			timer = setInterval( function() {
				if (window.scrollY > step) {
					window.scrollBy(0,-step);
				}
				else {
					window.scrollBy(0,-window.scrollY);
					clearInterval(timer);
				}
			}, interval)
		};

		if(hide) {
			var style = element[0].style,
					display = style.display;

			style.display = 'none';

			document.addEventListener('scroll', function() {
				if(window.scrollY > 0) {
					style.display = display;
				}
				else {
					style.display = 'none';
				}

			});


		}
		element.on('click', scrollToTop);
	};

	return {
		restrict: 'A',
		link: link
	}
});
