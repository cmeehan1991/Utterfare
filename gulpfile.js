const { series, parallel } = require('gulp');
const gulp = require('gulp');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const rename = require('gulp-rename');

function clean(cb){
	cb();
}

function build(cb){
	cb();
}

function minify_js(cb){
	
	gulp.src('assets/js/global/src/*.js')
	.pipe(concat('allscripts.js'))
	.pipe(gulp.dest('assets/js/global/dist'))
	.pipe(rename('allscripts.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('assets/js/global/dist'));
	
	cb();
}

function minify_styles(cb){
	cb();
}


exports.build = build;
exports.minify = series(minify_js);
exports.default = series(clean, build);
