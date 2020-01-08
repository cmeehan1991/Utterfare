import 'bootstrap';
import 'jquery';
import 'angular';
import 'angular-route';
import 'select2';

global.$ = global.jQuery = require('jquery');



//Frontend Scripts
require('./src/home.js');
require('./src/single.js');
require('./src/searchAnalytics.js');
require('./src/searchScript.js');

//Global scripts

require('./src/locationScript.js');
require('./src/angular.js');