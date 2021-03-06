/**
 * @file
 * Task: Lint: scss Files.
 */

module.exports = function (gulp, plugins, options) {
  'use strict';

  // Lint scss files and throw an error for a CI to catch.
  gulp.task('lint:css-with-fail', function () {
    return gulp.src(options.sass.files)
      .pipe(plugins.gulpStylelint({

        reporters: [
          {
            formatter: 'string',
            console: true,
            failAfterError: true
          }
        ]
      }))
  });
};
