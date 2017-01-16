'use strict';

var ui_bootstrap = require('angular-ui-bootstrap');

module.exports = angular.module('lexicon', [ui_bootstrap.name])
.controller('lexiconController', [  '$scope', function($scope) {
  $scope.categories = bonsumData.lexicon_categories;

  $scope.toggleAll = function(open) {
    angular.forEach($scope.categories, function(category) {
        angular.forEach(category.brands, function(brand) {
          brand.open = open;
        });
    });
  };

}]);
