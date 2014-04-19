module.exports = {

	js: {                       
		files: [ '../assets/js/admin/*.js' ],
		tasks: [
			'jshint',
			'uglify',
			'notify:js'
		],
	},
	sass: {

		files: ['../scss/*.scss'],
		tasks: [
			'compass',
			'cssmin',
		],
	},

	css: {
		files: ['*.css']
	},

	livereload: {
		files: ['../assets/css/*.css'],
		options: { livereload: true }
	}
	
};