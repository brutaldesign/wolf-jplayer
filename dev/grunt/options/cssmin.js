module.exports = {
	minify: {
		expand: true,
		cwd: '../assets/css/',
		src: ['*.css', '!*.min.css'],
		dest: '../assets/css/min/',
		ext: '.min.css'
	}
};
	