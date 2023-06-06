module.exports = function(grunt) {
	grunt.initConfig({
		compass: {                  // Task
			dev: {
				options: {
					httpPath: 'www/',
					sassDir: 'www/resources/css/sass',
					cssDir: 'www/resources/css',
					imagesPath: 'www/',
					generatedImagesPath: 'www/',
					noLineComments: false,
					debugInfo: true,
					outputStyle: 'nested',
					trace: true,
					watch: true
				}
			}
		},
		watch: {
			css: {
				files: 'www/resources/css/sass/*.scss',
				tasks: ['compass:dev']
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.registerTask('default', ['compass:dev']);
	grunt.registerTask('dev', ['compass:dev','watch']);
};