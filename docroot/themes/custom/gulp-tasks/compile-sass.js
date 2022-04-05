/**
 * @file
 * Task: Compile: Sass.
 */

const gulp = require("gulp");
module.exports = function (gulp, plugins, options) {
  'use strict';

  gulp.task('compile:sass', function () {
    return gulp.src([options.sass.files])
      .pipe(plugins.sourcemaps.init())
      .pipe(plugins.sassGlob())
      .pipe(plugins.sass({
        errLogToConsole: true,
        outputStyle: 'compressed',
      }))
      .pipe(plugins.postcss([plugins.autoprefixer({
        grid: true,
        cascade: false
      })]))
      .pipe(plugins.sourcemaps.write())
      .pipe(gulp.dest(options.sass.destination));
  });

  gulp.task('compile:sass-dev', function () {
    return gulp.src([options.sass.files])
      .pipe(plugins.sourcemaps.init())
      .pipe(plugins.sassGlob())
      .pipe(plugins.sass({
        errLogToConsole: true,
        outputStyle: 'expanded'
      }))
      .pipe(plugins.postcss([plugins.autoprefixer({
        grid: true,
        cascade: false
      })]))
      .pipe(plugins.sourcemaps.write())
      .pipe(gulp.dest(options.sass.destination));
  });
};
