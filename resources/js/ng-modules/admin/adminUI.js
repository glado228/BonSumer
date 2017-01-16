'use strict';

var ngGrid = require('angular-ng-grid');

module.exports = angular.module('admin.UI', [ngGrid.name])
.controller('AffiliateController', [
	'$scope',
	'bonsumData',
	'Utils',
	'$http',
	function($scope, bonsumData, Utils, $http) {


	var dataSource = {

		pageSize: bonsumData.merchant_transactions.pageSize,
		getRows: function(params) {

			$http.post(bonsumData.merchant_transactions.loadURL,
			{
				sortModel: params.sortModel,
				filterModel: params.filterModel,
				startRow: params.startRow,
				endRow: params.endRow
			}).then(
				function(response) {
					params.successCallback(response.data.rowsThisPage, response.data.lastRow);
					$scope.gridOptions.api.sizeColumnsToFit();
				},
				function(response) {
					params.failCallback();
					Utils.openDialogSimple('Error', 'The data could not be loaded');
				}
			);
		}
	};

	var overrideCellRenderer = function(params) {

		params.$scope.options = [];
		angular.forEach(bonsumData.merchant_transactions.status, function(v,i) {
			params.$scope.options.push({value: i, label: v});
		});

		var template = '<div ng-cloak ng-click="startEditing()" ng-focus="startEditing()" ng-show="!editing">'
		+ bonsumData.merchant_transactions.status[params.value] + '</div>'
		+ '<select ng-cloak class="form-control" ng-show="editing" ng-change="newValue()" ng-blur="closeEditor()" ng-model="data.status_override" ng-options="item.value as item.label for item in options"></select>';

		params.$scope.newValue = function() {

			Utils.httpWithDialog(bonsumData.merchant_transactions.updateStatusOverrideURL + '/' + params.data.id + '/' + params.data.status_override,
			null,
			function() {
				$scope.gridOptions.api.refreshView();
			},
			function(response) {
				Utils.openDialogSimple('Error', 'The transaction could not be updated');
			},
			function() {
				$scope.closeEditor();
			},
			'Updating transaction'
			);
		}

		params.$scope.closeEditor = function() {
			params.$scope.editing = false;
		}

		params.$scope.startEditing = function() {
			params.$scope.editing = true;
		};

		return template;
 	};

 	var overrideFilterCellRenderer = function(params) {

 		return bonsumData.merchant_transactions.status[params.value];
 	}

	var cellClass = function(params) {
		switch (params.data.internal_status) {
			case 2:
				return "bg-success";
			case 3:
				return "bg-danger";
		}
		return null;
	};

	var amountClass = function(params) {
		if (params.data.amount != params.data.original_amount) {
			return "text-danger bg-danger";
		}
		return cellClass(params);
	};

	var commissionClass = function(params) {
		if (params.data.commission != params.data.original_commission) {
			return "text-danger bg-danger";
		}
		return cellClass(params);
	};

	var valid_override_values = [];
	for (var index in bonsumData.merchant_transactions.status) {
		valid_override_values.push(index);
	}

	$scope.gridOptions = {

		enableColResize: true,
        enableServerSideFilter: true,
	    enableServerSideSorting: true,
	    datasource: dataSource,
        angularCompileRows: true,
		columnDefs: [
				{ headerName: "Network", field: "network", cellClass: cellClass, filter: "set", filterParams: {values: bonsumData.merchant_transactions.affiliateNetworks, newRowsAction: 'keep'} },
				{ headerName: "Program", field: "program_name", cellClass: cellClass, filter: "text", filterParams: {newRowsAction: 'keep'} },
				{ headerName: "UserID", field: "user_id", cellClass: cellClass, suppressMenu: true },
				{ headerName: "ShopID", field: "shop_id", cellClass: cellClass, suppressMenu: true },
				{ headerName: "Date",  field: "clickdate", cellClass: cellClass, suppressMenu: true, sort: "desc" },
				{ headerName: "Amount", field: "amount",  cellClass: amountClass, filter: "number", filterParams: {newRowsAction: 'keep'} },
				{ headerName: "Comm.", field: "commission", cellClass: commissionClass, filter: "number", filterParams: {newRowsAction: 'keep'} },
				{ headerName: "Bonets", field: "bonets", suppressSorting: true, suppressMenu: true, cellClass: commissionClass, filter: "number", filterParams: {newRowsAction: 'keep'} },
				{ headerName: "Or. Amount", field: "original_amount", headerTooltip: "Original amount", cellClass: cellClass, filter: "number", filterParams: {newRowsAction: 'keep'} },
				{ headerName: "Or. Comm.", field: "original_commission", headerTooltip: "Original commission", cellClass: cellClass, filter: "number", filterParams: {newRowsAction: 'keep'} },
				{ headerName: "Curr.", field: "currency", cellClass: cellClass, filter: "set", filterParams: {values: bonsumData.merchant_transactions.currencies, newRowsAction: 'keep'} },
				{ headerName: "Network Status", field: "network_status", cellClass: cellClass, filter: "set", filterParams: {newRowsAction: 'keep'} },
				{ headerName: "Override", field: "status_override", cellClass: cellClass, filter: "set",
					cellRenderer: overrideCellRenderer,
					 filterParams: {
					 	newRowsAction: 'keep',
					 	cellRenderer: overrideFilterCellRenderer,
					    values: valid_override_values
					}
				}
		],

		cellDoubleClicked: function(params) {
			if (params.data.user_id) {
				Utils.redirectTo(bonsumData.merchant_transactions.singleUserURL + '/' + params.data.user_id);
			} else {
				Utils.openDialogSimple('Error', 'No user associated with the selected transaction');
			}
		}
	};


}]).controller('ShopFormController',
	['$scope',
	'$controller',
	'$http',
	'$timeout',
	'Utils',
	'bonsumData',
	function($scope, $controller, $http, $timeout, Utils, bonsumData) {

		angular.extend(this, $controller('FormBaseController', {$scope: $scope}));

		$scope.Utils = Utils;
		$scope.affiliates = bonsumData.affiliates;
		$scope.rewardTypes = bonsumData.rewardTypes;
		$scope.shopTypes = bonsumData.shopTypes;
		$scope.REWARD_TYPE_PROPORTIONAL = bonsumData.REWARD_TYPE_PROPORTIONAL;
		$scope.REWARD_TYPE_FIXED = bonsumData.REWARD_TYPE_FIXED;

	    Utils.registerBeforeUnload(function() {
	    	return $scope.form.$dirty;
	    });

		$scope.formdata = bonsumData.shop || {};
		if (!($scope.formdata.shop_criteria instanceof Array)) {
			$scope.formdata.shop_criteria = [];
		}
		if (!($scope.formdata.shop_type instanceof Array)) {
			$scope.formdata.shop_type = [];
		}
		if (!$scope.formdata.affiliate) {
			$scope.formdata.affiliate = '';
		}
		$scope.vouchers = [];
		if ($scope.formdata.vouchers) {
			angular.forEach($scope.formdata.vouchers, function(v_entry) {
				var value = v_entry['value'];
				angular.forEach(v_entry['codes'], function(code) {
					$scope.vouchers.push({
						'code': code,
						'value': value
					});
				});
			});
		}
		delete $scope.formdata.vouchers;

		$scope.shop_criteria_labels = bonsumData.shopCriteria;
		$scope.shop_types_labels = bonsumData.shopTypes;

		$scope.FormBaseController = {
			serviceUrl: {
				url: (bonsumData.shop ? bonsumData.shopUpdateUrl + '/' + (bonsumData.shop.id || bonsumData.shop._id) : bonsumData.shopStoreUrl),
				method: (bonsumData.shop ? 'put' : 'post')
			},
			savingMessage: 'Saving shop',
			success: function(response) {
				Utils.redirectTo(bonsumData.backUrl);
			},
			error: function(response) {
				Utils.openDialogSimple('Operation failed', 'Please check the form for errors.');
			}
		};


		$scope.delete = function() {

			if (bonsumData.shop) {

				Utils.openDialog('Really delete?', 'The shop with ID ' + (bonsumData.shop.id || bonsumData.shop._id) + ' will be permanently deleted',
					function() {
						Utils.httpWithDialog({
							url: bonsumData.shopDeleteUrl + '/' + (bonsumData.shop.id || bonsumData.shop._id),
							method: 'delete'
						},
						null,
						function() {
							Utils.redirectTo(bonsumData.backUrl);
						},
						function() {
							Utils.openDialogSimple('Error', 'The shop could not be deleted');
						},
						null,
						'Deleting shop...');
				});
			}
		};

		$scope.changeThumbnail = function() {

			$timeout(function() {
				var user_input = prompt('Please enter a location for the thumbnail. You can either enter an external resource, like "http://www.myimages.com/apples.jpg", or a resource in our media library. The latter have to start with "/", like in "/home/apples.jpg".',
					$scope.formdata.thumbnail);
					$scope.formdata.thumbnail = user_input;
					$scope.form.$setDirty();
					$scope.changed();
			});
		};

		$scope.changeMouseoverThumbnail = function() {

			$timeout(function() {
				var user_input = prompt('Please enter a location for the mouseover thumbnail. You can either enter an external resource, like "http://www.myimages.com/apples.jpg", or a resource in our media library. The latter have to start with "/", like in "/home/apples.jpg".',
					$scope.formdata.thumbnail_mouseover);
					$scope.formdata.thumbnail_mouseover = user_input;
					$scope.form.$setDirty();
					$scope.changed();
			});
		};

		$scope.vouchersGridOptions = {
			ready: function() { $scope.vouchersGridOptions.api.sizeColumnsToFit(); },
	        enableColResize: true,
			enableSorting: true,
			enableFilter: true,
	        rowSelection: 'single',
			columnDefs: [
				{ headerName: "Voucher Code", field: "code", filter: "text" },
				{ headerName: "Value", field: "value", filter: "number", sort: "desc" },
			],
			rowData: $scope.vouchers
		};

		function parseCodes(raw_codes) {

			var codes = [];
			angular.forEach(raw_codes.split(','), function(value) {
				var val = value.trim();
				if (val) {
					codes.push(val);
				}
			});
			return codes;
		}

		var initNewVoucherForm = function() {
			$scope.new_voucher_form = {};
			if (!$scope.errors) {
				$scope.errors = [];
			}
		};

		initNewVoucherForm();

		$scope.newVoucherChanged = function() {

			console.dir($scope.vouchersGridOptions);

			$http.post(bonsumData.addVoucherUrl + '/' + bonsumData.shop._id,
				{
					id: $scope.formdata._id,
					codes: parseCodes($scope.new_voucher_form.codes),
					value: $scope.new_voucher_form.value,
					validate: true
				},
				{
					timeout: 5000
				}
			).then(function(response) {
					$scope.errors.vouchers = null;
				},
				function(response) {
					$scope.errors.vouchers = response.data;
				}
			);
		};

		$scope.addVoucher = function() {
			var value = $scope.new_voucher_form.value;
			var codes = parseCodes($scope.new_voucher_form.codes);

			Utils.httpWithDialog(
				bonsumData.addVoucherUrl + '/' + bonsumData.shop._id,
				{
					codes: codes,
					value: value
				},
				function() {
					$scope.errors.vouchers = null;

					angular.forEach(codes, function(code) {
						$scope.vouchers.push({
							code: code,
							value: value
						});
					});
					$scope.voucher_added = true;
					$timeout(function() {
						$scope.voucher_added = false;
					}, 1000);
					initNewVoucherForm();
					$scope.vouchersGridOptions.api.setRows($scope.vouchers);
				},
				function(response) {
					$scope.errors.vouchers = response.data;
					Utils.openDialogSimple('Could not add voucher', 'Please check the form for errors.');
				},
				function() {
					$scope.voucherOp = false;
				},
				'Saving voucher',
				function() {
					$scope.voucherOp = true;
				}
			);
		};

		$scope.deleteVoucher = function() {

			if ($scope.vouchersGridOptions.selectedRows[0]) {

				var code = $scope.vouchersGridOptions.selectedRows[0].code;
				Utils.httpWithDialog(
					bonsumData.deleteVoucherUrl + '/' + bonsumData.shop._id,
					{
						code: code
					},
					function() {
						$scope.errors.delete_voucher = null;
						var new_vouchers = [];
						angular.forEach($scope.vouchers, function(e) {
							if (e.code != code.trim()) {
								new_vouchers.push(e);
							}
						});
						$scope.vouchers = new_vouchers;
						$scope.voucher_deleted = true;
						$timeout(function() {
							$scope.voucher_deleted = false;
						}, 1000);
						$scope.vouchersGridOptions.api.setRows($scope.vouchers);
					},
					function(response) {
						$scope.errors.delete_voucher = response.data;
						Utils.openDialogSimple('Could not delete voucher', 'Please check the form for errors.');
					},
					function() {
						$scope.voucherOp = false;
					},
					'Deleting voucher',
					function() {
						$scope.voucherOp = true;
					}
				);

			}
		};

	}
])
.controller('DonationOptionFormController',
	['$scope',
	'$controller',
	'$http',
	'$timeout',
	'Utils',
	'bonsumData',
	function($scope, $controller, $http, $timeout, Utils, bonsumData) {

		angular.extend(this, $controller('FormBaseController', {$scope: $scope}));

		$scope.Utils = Utils;

	    Utils.registerBeforeUnload(function() {
	    	return $scope.form.$dirty;
	    });

		$scope.formdata = bonsumData.donation || {};


		$scope.FormBaseController = {
			serviceUrl: {
				url: (bonsumData.donation ? bonsumData.donationUpdateUrl + '/' + (bonsumData.donation.id || bonsumData.donation._id) : bonsumData.donationStoreUrl),
				method: (bonsumData.donation ? 'put' : 'post')
			},
			savingMessage: 'Saving donation option',
			success: function(response) {
				Utils.redirectTo(bonsumData.backUrl);
			},
			error: function(response) {
				Utils.openDialogSimple('Operation failed', 'Please check the form for errors.');
			},
			preprocessForm: function(data, validate) {
				angular.forEach(data.donation_sizes, function(el) {
					el.text = parseInt(el.text);
				});
				return data;
			}
		};


		$scope.delete = function() {

			if (bonsumData.donation) {

				Utils.openDialog('Really delete?', 'The donation option with ID ' + (bonsumData.donation.id || bonsumData.donation._id) + ' will be permanently deleted',
					function() {
						Utils.httpWithDialog({
							url: bonsumData.donationDeleteUrl + '/' + (bonsumData.donation.id || bonsumData.donation._id),
							method: 'delete'
						},
						null,
						function() {
							Utils.redirectTo(bonsumData.backUrl);
						},
						function() {
							Utils.openDialogSimple('Error', 'The donation option could not be deleted');
						},
						null,
						'Deleting option...');
				});
			}
		};

		$scope.changeThumbnail = function() {

			$timeout(function() {
				var user_input = prompt('Please enter a location for the thumbnail. You can either enter an external resource, like "http://www.myimages.com/apples.jpg", or a resource in our media library. The latter have to start with "/", like in "/home/apples.jpg".',
				$scope.formdata.thumbnail);
				$scope.formdata.thumbnail = user_input;
				$scope.form.$setDirty();
				$scope.changed();
			});
		};

		$scope.changeMouseoverThumbnail = function() {

			$timeout(function() {
				var user_input = prompt('Please enter a location for the mouseover thumbnail. You can either enter an external resource, like "http://www.myimages.com/apples.jpg", or a resource in our media library. The latter have to start with "/", like in "/home/apples.jpg".',
				$scope.formdata.thumbnail_mouseover);
				$scope.formdata.thumbnail_mouseover = user_input;
				$scope.form.$setDirty();
				$scope.changed();
			});
		};

	}
]).controller('UserListController',
[ '$scope',
'$http',
'bonsumData',
'Utils',
function($scope, $http, bonsumData, Utils) {

		var dataSource = {

			pageSize: bonsumData.users.pageSize,
			getRows: function(params) {

				$http.post(bonsumData.users.loadURL,
				{
					sortModel: params.sortModel,
					filterModel: params.filterModel,
					startRow: params.startRow,
					endRow: params.endRow
				}).then(
					function(response) {
						params.successCallback(response.data.rowsThisPage, response.data.lastRow);
						$scope.gridOptions.api.sizeColumnsToFit();
					},
					function(response) {
						params.failCallback();
						Utils.openDialogSimple('Error', 'The data could not be loaded');
					}
				);
			}
		};

		var cellRules = { 'bg-danger': 'data.disabled', 'bg-warning': '!data.confirmed' };
		$scope.gridOptions = {

	        enableColResize: true,
	        enableServerSideFilter: true,
		    enableServerSideSorting: true,
		    datasource: dataSource,
			pinnedColumnCount: 2,
		    columnDefs: [
				{ headerName: "Id", field: "id", suppressMenu: true,
					cellClassRules: cellRules
				},
				{ headerName: "Email", field: "email", filter: "text", filterParams: {newRowsAction: 'keep'},
					cellClassRules: cellRules
				},
				{ headerName: "First Name", field: "firstname", filter: "text", filterParams: {newRowsAction: 'keep'},
					cellClassRules: cellRules
				},
				{ headerName: "Last Name", field: "lastname", filter: "text", filterParams: {newRowsAction: 'keep'}, cellClassRules: cellRules
				},
				{ headerName: "Gender", field: "gender", filter: "set", filterParams: {newRowsAction: 'keep', values: ['M', 'F']},
					cellClassRules: cellRules
				},
				{ headerName: "Locale", field: "preferred_locale", filter: "set", filterParams: {values: bonsumData.users.locales, newRowsAction: 'keep'},
					cellClassRules: cellRules
				},
				{ headerName: 'Bonets', field: "bonets", filter: "number", filterParams: {newRowsAction: 'keep'},
					cellClassRules: cellRules
				},
				{ headerName: "Admin", field: "admin", suppressMenu: true, cellClassRules: cellRules },
				{ headerName: "Confirmed", field: "confirmed", suppressMenu: true, cellClassRules: { 'bg-danger': 'data.disabled', 'bg-warning text-danger': '!data.confirmed' } },
				{ headerName: "Created on", field: "created_at", suppressMenu: true, cellClassRules: cellRules, sort: 'desc' },
				{ headerName: "Disabled", field: "disabled", suppressMenu: true, cellClassRules: { 'bg-danger text-danger': 'data.disabled', 'bg-warning': '!data.confirmed' } },
				{ headerName: "Disabled on", field: "disabled_at", cellClassRules: cellRules, valueGetter: 'data.disabled ? data.disabled_at : "NA"', suppressMenu: true }
			],

			cellDoubleClicked: function(params) {
				Utils.redirectTo(bonsumData.users.singleUserURL + '/' + params.data.id);
			}
		};

}])
.controller('UserFormController',
	['$scope',
	'$controller',
	'$http',
	'$timeout',
	'Utils',
	'bonsumData',
	function($scope, $controller, $http, $timeout, Utils, bonsumData) {


		$scope.user = bonsumData.user;
		$scope.bonets_credit = 0;
		$scope.currentUser = bonsumData.currentUser;
		$scope.setDisabledURL = bonsumData.setDisabledURL;
		$scope.deleteUserURL = bonsumData.deleteUserURL;
		$scope.setAdminURL = bonsumData.setAdminURL;
		$scope.resetPasswordURL = bonsumData.resetPasswordURL;
		$scope.creditBonetsURL = bonsumData.creditBonetsURL;
		$scope.sendConfirmationReminderURL = bonsumData.sendConfirmationReminderURL;

		$scope.delete = function() {
			Utils.openDialog('Really delete?', 'The user with ID ' + ($scope.user.id) + ' will be permanently deleted',
				function() {
					Utils.httpWithDialog({
						url: $scope.deleteUserURL + '/' + $scope.user.id,
						method: 'delete'
					},
					null,
					function() {
						Utils.redirectTo(bonsumData.backURL);
					},
					function() {
						Utils.openDialogSimple('Error', 'The user could not be deleted');
					},
					null,
					'Deleting user...');
			});
		};

	    $scope.toggleDisabled = function() {

			Utils.openDialog('Please confirm', 'Do you really want to '+ ($scope.user.disabled ? ' activate ' : ' disable ') + ' this user?',
				function() {
			    	Utils.httpWithDialog($scope.setDisabledURL + '/' + ($scope.user.disabled ? 0 : 1),
			    	null,
					function(response) {
						$scope.user = response.data.user;
					},
					function(response) {
						Utils.openDialogSimple('Error', 'The operation could not be completed');
					},
					function() {
						$scope.isSaving = false;
					},
					null,
					function() {
						$scope.isSaving = true;
					});
	    	});
	   };

	   $scope.creditBonets = function() {

	   		//$scope.bonets_credit = parseInt($scope.bonets_credit);

	   		Utils.httpWithDialog($scope.creditBonetsURL,
	   		{
	   			bonets: $scope.bonets_credit,
	   			bonets_credit_message: $scope.bonets_credit_message
	   		},
	   		function (response) {
				Utils.openDialogSimple('Success', $scope.bonets_credit + ' bonets have been credited');
	   			$scope.user = response.data.user;
	   			$scope.bonets_credit = 0;
	   			$scope.bonets_credit_message = null;
	   		},
	   		function (response) {
				Utils.openDialogSimple('Error', 'The bonets could not be credited');
	   			$scope.errors = response.data;
	   		},
	   		function() {
				$scope.isSaving = false;
			},
			null,
			function() {
				$scope.isSaving = true;
			});
	   };

      $scope.toggleAdmin = function() {

		Utils.openDialog('Please confirm', 'Do you really want to '+ ($scope.user.admin ? ' revoke ' : ' grant ') + ' this user admin rights?',
				function() {
			    	Utils.httpWithDialog($scope.setAdminURL + '/' + ($scope.user.admin ? 0 : 1),
			    	null,
					function(response) {
						$scope.user = response.data.user;
					},
					function(response) {
						Utils.openDialogSimple('Error', 'The operation could not be completed');
					},
					function() {
						$scope.isSaving = false;
					},
					null,
					function() {
						$scope.isSaving = true;
					}
			    	);
	    	});
	 	}

      $scope.resetPassword = function() {

		Utils.openDialog('Please confirm', 'Do you really want to reset this user\'s password?',
			function() {
		    	Utils.httpWithDialog($scope.resetPasswordURL,
		    	null,
				function(response) {
					Utils.openDialogSimple('Ok', 'The password was reset');
					$scope.user = response.data.user;
				},
				function(response) {
					Utils.openDialogSimple('Error', 'The operation could not be completed');
				},
				function() {
					$scope.isSaving = false;
				},
				null,
				function() {
					$scope.isSaving = true;
				}
				);
	    	}
	    );
	   };

	   $scope.sendConfirmationReminder = function() {


		Utils.openDialog('Please confirm', 'Do you really want to send this user a confirmation reminder?',
			function() {
		    	Utils.httpWithDialog($scope.sendConfirmationReminderURL,
		    	null,
				function(response) {
					Utils.openDialogSimple('Ok', 'A confirmation reminder was sent');
					$scope.user = response.data.user;
				},
				function(response) {
					Utils.openDialogSimple('Error', 'The operation could not be completed');
				},
				function() {
					$scope.isSaving = false;
				},
				null,
				function() {
					$scope.isSaving = true;
				}
				);
	    	}
	    );
	   };
	}
])
.controller('DonationController', [
	'$scope',
	'bonsumData',
	'Utils',
	'$http',
	function($scope, bonsumData, Utils, $http) {

	var dataSource = {

		pageSize: bonsumData.donations.pageSize,
		getRows: function(params) {

			$http.post(bonsumData.donations.loadURL,
			{
				sortModel: params.sortModel,
				filterModel: params.filterModel,
				startRow: params.startRow,
				endRow: params.endRow
			}).then(
				function(response) {
					params.successCallback(response.data.rowsThisPage, response.data.lastRow);
					$scope.gridOptions.api.sizeColumnsToFit();
				},
				function(response) {
					params.failCallback();
					Utils.openDialogSimple('Error', 'The data could not be loaded');
				}
			);
		}
	};

	$scope.gridOptions = {

		enableColResize: true,
        enableServerSideFilter: true,
	    enableServerSideSorting: true,
	    datasource: dataSource,
		columnDefs: [
				{ headerName: "Receiver", field: "receiver", suppressSorting:true, suppressMenu:true  },
				{ headerName: "UserID", field: "user_id", suppressMenu: true },
				{ headerName: "Date", field: "date", sort: "desc", suppressMenu: true },
				{ headerName: "Amount", field: "amount", filter: "number", filterParams: { newRowsAction: 'keep'} },
				{ headerName: "Currency", field: "currency", filter: "set", filterParams: {values: bonsumData.donations.currencies, newRowsAction: 'keep'} },
				{ headerName: "Bonets", field: "bonets", filter: "number", filterParams: { newRowsAction: 'keep'} }
		],

		cellDoubleClicked: function(params) {
			if (params.data.user_id) {
				Utils.redirectTo(bonsumData.donations.singleUserURL + '/' + params.data.user_id);
			} else {
				Utils.openDialogSimple('Error', 'No user associated with the selected donation');
			}
		}
	};

}])
.controller('SyncController', ['$scope', '$http', '$interval', 'bonsumData',
	function($scope, $http, $interval, bonsumData) {

		function scrollSyncMediaOutput() {
			var textarea = document.getElementById('sync-media-output-area');
			textarea.scrollTop = textarea.scrollHeight;
		}

		$scope.my_email = bonsumData.currentUser.email;

		var checking_lock = false;
		$interval(function() {

			if (!checking_lock) {
				checking_lock = true;

				$http.get(bonsumData.syncMediaLockUrl)
				.success(function(data, status) {
					$scope.lockedby = data;
				})
				.finally(function() {
					checking_lock = false;
				});
			}

		}, 2000);

		$scope.syncMedia = function() {

			if (!$scope.media_pending) {

				$scope.media_success = $scope.media_error = false;
				$scope.media_pending = true;
				$scope.syncOutput = '';

				$http.post(bonsumData.syncMediaUrl)
				.success(function(data, status) {
					$scope.syncOutput = data;
					$scope.media_success = true;
				})
				.error(function(data, status) {
					$scope.syncOutput = 'Status: ' + status + '. ' + data;
					$scope.media_error = true;
				}).finally(function() {
					$scope.media_pending = false;
					scrollSyncMediaOutput();
				});
			}
		};


}]);
