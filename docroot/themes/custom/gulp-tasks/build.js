/**
 * @file
 * Task: Build.
 */

module.exports = function (gulp) {
  'use strict';

  gulp.task('build', gulp.parallel(
    'compile:sass',
  ));

  gulp.task('build:dev', gulp.parallel(
    'compile:sass-dev',
  ));

  gulp.task('lint', gulp.parallel(
    'lint:js-with-fail',
    'lint:css-with-fail',
  ));
};
