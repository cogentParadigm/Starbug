/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    less: {
      "starbug-1": {
        files: {
          "public/stylesheets/dist/screen.css": "public/stylesheets/src/screen.less"
        }
      }
    },
    postcss: {
      options: {
        map: {inline: false},
        parser: require("postcss-scss"),
        processors: [
          require("precss")(),
          require('postcss-url')({url: 'rebase'}),
          require("postcss-calc")(),
          require("lost")(),
          require("postcss-cssnext")(),
          require("cssnano")({filterPlugins: false, autoprefixer: false, discardComments:{removeAll:true}})
        ]
      },
      "starbug-1": {
        src: "public/stylesheets/src/screen.css",
        dest: "public/stylesheets/dist/screen.css"
      }
    },
    watch: {
      "starbug-1": {
        files: ["public/stylesheets/src/**/*"],
        tasks: ["build:starbug-1"]
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-postcss');

  grunt.registerTask('build', function(target) {
    grunt.task.run.apply(grunt.task, ['less', 'postcss'].map(function(task) {
      return (target == null) ? task : task + ':' + target
    }));
  });
};
