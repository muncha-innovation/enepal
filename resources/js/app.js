require('./bootstrap');

import Alpine from 'alpinejs';
import jquery from 'jquery';
import * as pdfjsLib from 'pdfjs-dist';

window.Alpine = Alpine;
window.$ = window.Jquery = window.jQuery = jquery;
Alpine.start();

require('gasparesganga-jquery-loading-overlay')

window.pdfjsLib = pdfjsLib

