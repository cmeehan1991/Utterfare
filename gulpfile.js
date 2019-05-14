const { series, parallel } = require('gulp');

function clean(cb){
	cb();
}

function build(cb){
	cb();
}

function minify_js(cb){
	cb();
}

function minify_styles(cb){
	cb();
}


exports.build = build;
exports.minify = parallel(minify_js, minify_styles);
exports.default = series(clean, build);
