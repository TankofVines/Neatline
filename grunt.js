
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Grunt file.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

module.exports = function(grunt) {

  // Load custom tasks.
  grunt.loadNpmTasks('grunt-css');
  grunt.loadNpmTasks('grunt-contrib-stylus');
  grunt.loadNpmTasks('grunt-clean');
  grunt.loadNpmTasks('grunt-shell');

  // Load configuration.
  var config = grunt.file.readJSON('./config.json');

  grunt.initConfig({

    shell: {

      npm_tests_map: {
        command: 'npm install',
        stdout: true,
        execOptions: {
          cwd: config.jasmine.map
        }
      },
      npm_tests_editor: {
        command: 'npm install',
        stdout: true,
        execOptions: {
          cwd: config.jasmine.editor
        }
      },

      bower_app: {
        command: 'bower install',
        stdout: true,
        execOptions: {
          cwd: config.bower.app
        }
      },
      bower_tests: {
        command: 'bower install',
        stdout: true,
        execOptions: {
          cwd: config.bower.tests
        }
      },

      build_openlayers: {
        command: 'python build.py full OpenLayers.js',
        stdout: true,
        execOptions: {
          cwd: config.build.openlayers
        }
      },
      build_bootstrap: {
        command: 'make bootstrap',
        stdout: true,
        execOptions: {
          cwd: config.build.bootstrap
        }
      },
      move_bootstrap_images: {
        command: 'cp -r img ../../../css/img',
        stdout: true,
        execOptions: {
          cwd: config.build.bootstrap
        }
      },

      phpunit: {
        command: 'phpunit --color',
        stdout: true,
        execOptions: {
          cwd: './tests'
        }
      },
      jasmine_map: {
        command: 'grunt jasmine',
        stdout: true,
        execOptions: {
          cwd: config.jasmine.map
        }
      },
      jasmine_map_server: {
        command: 'grunt jasmine-server',
        stdout: true,
        execOptions: {
          cwd: config.jasmine.map
        }
      },
      jasmine_editor: {
        command: 'grunt jasmine',
        stdout: true,
        execOptions: {
          cwd: config.jasmine.editor
        }
      },
      jasmine_editor_server: {
        command: 'grunt jasmine-server',
        stdout: true,
        execOptions: {
          cwd: config.jasmine.editor
        }
      }

    },

    clean: {
      npm_tests_map: config.jasmine.map+'/node_modules',
      npm_tests_editor: config.jasmine.editor+'/node_modules',
      bower_app: config.bower.app+'/components',
      bower_tests: config.bower.tests+'/components',
      payloads_css: config.payloads.css,
      payloads_js: config.payloads.js
    },

    concat: {
      neatline: {
        src: [

          // Vendor:
          config.vendor.js.jquery,
          config.vendor.js.underscore,
          config.vendor.js.backbone,
          config.vendor.js.eventbinder,
          config.vendor.js.wreqr,
          config.vendor.js.marionette,
          config.vendor.js.openlayers,
          config.vendor.js.bootstrap,
          config.vendor.js.d3,

          // Neatline:
          config.app+'/app.js',
          config.app+'/modules/map/**/*.js'

        ],
        dest: config.payloads.js+'/neatline.js',
        separator: ';'
      },
      editor: {
        src: [

          // Vendor:
          '<config:concat.neatline.src>',
          config.vendor.js.underscore_s,

          // Editor:
          config.app+'/modules/editor/**/*.js'

        ],
        dest: config.payloads.js+'/editor.js',
        separator: ';'
      },
      neatline_css: {
        src: [
          config.payloads.css+'/neatline.css',
          config.vendor.css.openlayers,
          config.vendor.css.bootstrap
        ],
        dest: config.payloads.css+'/neatline.css'
      },
      editor_css: {
        src: [
          '<config:concat.neatline_css.src>',
          config.payloads.css+'/overrides.css',
          config.payloads.css+'/editor.css'
        ],
        dest: config.payloads.css+'/editor.css'
      }
    },

    min: {
      neatline: {
        src: ['<config:concat.neatline.src>'],
        dest: config.payloads.js+'/neatline.js',
        separator: ';'
      },
      editor: {
        src: ['<config:concat.editor.src>'],
        dest: config.payloads.js+'/editor.js',
        separator: ';'
      }
    },

    stylus: {
      compile: {
        options: {},
        files: {
          'views/shared/css/payloads/*.css': [
            config.stylus+'/neatline.styl',
            config.stylus+'/editor.styl',
            config.stylus+'/overrides.styl'
          ]
        }
      }
    },

    watch: {
      files: [
        '<config:concat.neatline.src>',
        '<config:concat.editor.src>',
        config.stylus+'/*.styl'
      ],
      tasks: [
        'concat:neatline',
        'concat:editor',
        'stylus',
        'concat:neatline_css',
        'concat:editor_css'
      ]
    }

  });


  // Task aliases.
  // -------------

  // Default task.
  grunt.registerTask('default', 'test');

  // Build the application.
  grunt.registerTask('build', [
    'clean',
    'shell:npm_tests_map',
    'shell:npm_tests_editor',
    'shell:bower_app',
    'shell:bower_tests',
    'shell:build_openlayers',
    'shell:build_bootstrap',
    'shell:move_bootstrap_images',
    'min:neatline',
    'min:editor',
    'stylus',
    'concat:neatline_css',
    'concat:editor_css'
  ]);

  // Run all tests.
  grunt.registerTask('test', [
    'shell:phpunit',
    'shell:jasmine_map',
    'shell:jasmine_editor'
  ]);

  // Run Jasmine servers.
  grunt.registerTask('map_server', 'shell:jasmine_map_server');
  grunt.registerTask('editor_server', 'shell:jasmine_editor_server');


};
