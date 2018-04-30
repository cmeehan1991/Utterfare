module.exports = function(grunt){
	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify:{
			options:{
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			build:{
				src:'assets/js/global/src/*.js',
				dest: 'assets/js/global/dist/min/allscripts.min.js'
			},
		},
		cssmin:{
			options:{
				mergeIntoShorthands: false,
				roundingPrecision: -1
			},
			combine: {
				files:{
					'assets/styles/css/dist/min/allstyles.min.css': 'assets/styles/css/src/*.css',
				},
			},
		},
		watch: {
			scripts: {
				files:['assets/js/global/src/*.js', 'assets/styles/css/src/*.css'],
				tasks: ['uglify', 'cssmin'],
				options: {
					spawn: false,
				},
			},
		},
	});
	
	// Load the plugin that provides the "uglify" task
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	
	// Default task(s)
	grunt.registerTask('minify-js', ['uglify']);
	grunt.registerTask('minify-css', ['cssmin']);
	grunt.registerTask('minify-all', ['uglify', 'cssmin']);
	grunt.registerTask('watch', ['watch'])
};