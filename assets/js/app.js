/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
var $ = require('jquery');
require('bootstrap');
require('popper.js');
// var dt = require( 'datatables.net' )( window, $ );

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

// $(document).ready( function () {
//     $('#example').dataTable();
// } );

console.log('test');