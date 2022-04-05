/**
 * @file
 * Gulp file to call all the gulp related task.
 */

var gulp = require('gulp');

var plugins = {
  sass: require('gulp-sass')(require('node-sass')),
  sassGlob: require('gulp-sass-glob'),
  sassVariables: require('gulp-sass-variables'),
  sourcemaps: require('gulp-sourcemaps'),
  cleanCss: require('gulp-clean-css'),
  rename: require('gulp-rename'),
  postcss: require('gulp-postcss'),
  gulpStylelint: require('gulp-stylelint'),
  autoprefixer: require('autoprefixer'),
  plumber: require('gulp-plumber'),
  runSequence: require('run-sequence'),
  importOnce: require('node-sass-import-once'),
  eslint: require('gulp-eslint'),
};

// Used to generate relative paths for style guide output.
var path = require('path');

// These are used in the options below.
var paths = {
  styles: {
    source: 'sass/',
    destination: 'css/',
    variables: {
      $dir: 'rtl'
    }
  },
  scripts: 'js/',
  images: 'img/',
};

// These are passed to each task.
var options = {

  // ----- CSS ----- //

  css: {
    files: paths.styles.destination + '**/*.css',
    file: paths.styles.destination + '/styles.css',
    destination: paths.styles.destination
  },

  // ----- Sass ----- //

  sass: {
    files: paths.styles.source + '**/*.scss',
    file: paths.styles.source + '*.scss',
    directionalSource: paths.styles.directionalSource,
    directionalDestination: paths.styles.directionalDestination,
    variables: paths.styles.variables,
    destination: paths.styles.destination
  },

  // ----- JS ----- //
  js: {
    files: paths.scripts + '**/*.js',
    destination: paths.scripts

  },

  // ----- Images ----- //
  images: {
    files: paths.images + '**/*.{png,gif,jpg,svg}',
    destination: paths.images
  },

  // ----- eslint ----- //
  jsLinting: {
    files: {
      theme: [
        paths.scripts + '**/*.js',
        '!' + paths.scripts + '**/*.min.js'
      ],
      gulp: [
        'gulpfile.js',
        'gulp-tasks/**/*'
      ]
    }

  },

};

// Tasks
require('../gulp-tasks/compile-sass')(gulp, plugins, options);

require('../gulp-tasks/lint-js')(gulp, plugins, options);
require('../gulp-tasks/lint-css')(gulp, plugins, options);

require('../gulp-tasks/build')(gulp, plugins, options);
require('../gulp-tasks/default')(gulp, plugins, options);
