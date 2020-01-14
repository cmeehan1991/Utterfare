const { series, parallel } = require('gulp');
const gulp = require('gulp');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const rename = require('gulp-rename');
const ngAnnotate = require('gulp-ng-annotate');
const cleanCSS = require('gulp-clean-css');

function clean(cb){
	cb();
}

function build(cb){
	cb();
}

function minify_js(cb){
	
	gulp.src('assets/js/global/dist/app.js')
	.pipe(concat('allscripts.js'))
	.pipe(ngAnnotate({
		add:true
	}))
	.pipe(gulp.dest('assets/js/global/dist'))
	.pipe(rename('allscripts.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('assets/js/global/dist'));
	
	cb();
}

function minify_styles(cb){
	gulp.src('assets/styles/css/dist/app.css')
	.pipe(concat('allstyles.css'))
	.pipe(gulp.dest('assets/styles/css/dist'))
	.pipe(rename('allstyles.min.css'))
	.pipe(cleanCSS({compatibility: 'ie8'}))
	.pipe(gulp.dest('assets/styles/css/dist'));
	cb();
}


exports.build = build;
exports.minify = series(minify_js, minify_styles);
exports.default = series(clean, build);
