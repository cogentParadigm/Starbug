/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    postcss: {
      options: {
        map: {inline: false},
        parser: require("postcss-scss"),
        processors: [
          require("postcss-import"),
          require("postcss-import-url")({modernBrowser: true}),
          require("postcss-at-rules-variables")({
            atRules: ['for', 'if', 'else', 'each', 'mixin', 'custom-media', 'include']
          }),
          require("precss")({
            features: {
              'color-mod-function': { unresolved: 'warn' }
            }
          }),
          require('postcss-url')([
            {
              filter: '**/fontawesome-webfont.*',
              url: function(asset, dir) {
                return "//netdna.bootstrapcdn.com/font-awesome/4.7.0/fonts/" + asset.url.split('/').pop();
              }
            },
            { url: 'rebase'}
          ]),
          require("postcss-calc")(),
          require("cssnano")({filterPlugins: false, autoprefixer: false, discardComments:{removeAll:true}})
        ]
      },
      screen: {
        src: "public/stylesheets/src/screen.css",
        dest: "public/stylesheets/dist/screen.css"
      }
    },
    watch: {
      screen: {
        files: ["public/stylesheets/src/**/*"],
        tasks: ["build:screen"]
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('@lodder/grunt-postcss');

  grunt.registerTask('build', function(target) {
    grunt.task.run.apply(grunt.task, ['postcss'].map(function(task) {
      return (target == null) ? task : task + ':' + target
    }));
  });
};
