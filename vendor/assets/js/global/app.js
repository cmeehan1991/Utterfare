import 'bootstrap';
import 'jquery';
import 'angular';
import 'angular-route';
import 'select2';

global.$ = global.jQuery = require('jquery');


//Backend scripts
require('./src/AddEditItems.js');
require('./src/companyInformation.js');
require('./src/vendor.js');
require('./src/vendorInformation.js');
require('./src/VendorRegistration.js');
require('./src/VendorSignIn.js');

//Global scripts
require('./src/angular.js');