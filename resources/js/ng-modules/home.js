'use strict';

var ui_bootstrap = require('angular-ui-bootstrap');

module.exports = angular.module('home', [ui_bootstrap.name])
.controller('homeController', [  '$scope', 'Utils', 'Capabilities', 'bonsumData', function($scope, Utils, Capabilities, bonsumData) {
  $scope.tabs = [
    {
      active: true,
      url: bonsumData.shopUrl
    },
    {
      active: false,
      url: bonsumData.shopUrl
    },
    {
      active: false,
      url: bonsumData.redeemUrl
    },
  ];

  $scope.toggleOnHover = function(index) {
		//this event handler will never run on
		//touch screens, so that we will be able to
		//switch to the tab without also following
		//the link
    if(!Capabilities.supports.touchEvents &&
			 !$scope.tabs[index].active) {
      for(var i=0; i<$scope.tabs.length; i++) {
        $scope.tabs[i].active = (i === index);
      }
    }
  };

  $scope.tabClicked = function(index) {
		var active_tab = -1;

		for(var i=0; i<$scope.tabs.length; i++) {
			if($scope.tabs[i].active) {
				active_tab = i;
				break;
			}
		}
		if(active_tab === index) {
			Utils.redirectTo($scope.tabs[index].url);
		}
  };
}])
.controller('NavbarController', ['$scope', 'Utils',
	function($scope, Utils) {

		$scope.logout = Utils.logout;
	}
])
;
