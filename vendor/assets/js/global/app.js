import 'bootstrap';
import 'jquery';
import 'angular';
import 'angular-route';
import 'select2';
import '@fortawesome/fontawesome-free/js/fontawesome';
import '@fortawesome/fontawesome-free/js/solid';
import '@fortawesome/fontawesome-free/js/regular';
import '@fortawesome/fontawesome-free/js/brands';

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