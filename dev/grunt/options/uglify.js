module.exports = {
	
	options: {
		mangle: true,
		banner : '/*! <%= app.name %> v<%= app.version %> */\n'
	},

	dist: {
		files: {
'../assets/js/min/tipsy.min.js': [ '../assets/js/admin/tipsy.js'],
'../assets/js/min/colorpicker.min.js': [ '../assets/js/admin/colorpicker.js'],
'../assets/js/min/upload.min.js': [ '../assets/js/admin/upload.js'],
'../assets/js/min/jquery.jplayer.concat.min.js':  ['../assets/js/src/jquery.jplayer.min.js', '../assets/js/src/jplayer.playlist.js','../assets/js/jquery.jplayer.custom.js',],
		
		}
	}
	
};