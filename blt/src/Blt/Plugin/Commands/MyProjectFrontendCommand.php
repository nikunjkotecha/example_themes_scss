<?php

namespace MyProject\Blt\Plugin\Commands;

use Acquia\Blt\Robo\BltTasks;
use Symfony\Component\Finder\Finder;

/**
 * Defines commands in the "fe" namespace.
 */
class MyProjectFrontendCommand extends BltTasks {

  /**
   * Directories to ignore for Theme.
   *
   * @var string[]
   */
  protected static $themeIgnoredDirs = [
    'node_modules',
  ];

  /**
   * Setup Themes.
   *
   * @command fe:setup-themes
   * @aliases setup-themes
   */
  public function setupThemes() {
    $dir = $this->getConfigValue('docroot') . '/themes/custom';
    $task = $this->taskExec("cd $dir; npm install --unsafe-perm=true");
    return $task->run();
  }

  /**
   * Build styles for all the themes.
   *
   * @command fe:build-all-themes
   * @aliases build-all-themes
   * @description Build themes for all the themes.
   */
  public function buildStyles() {
    $exitCode = 0;
    $ignoredDirs = ['example_subtheme', 'node_modules', 'gulp-tasks'];

    $dir = $this->getConfigValue('docroot') . '/themes/custom/';

    /** @var \DirectoryIterator $subDir */
    foreach (new \DirectoryIterator($dir) as $subDir) {
      if ($subDir->isDir()
        && !$subDir->isDot()
        && !(strpos($subDir->getBasename(), '.') === 0)
        && !in_array($subDir->getBasename(), $ignoredDirs)
        && file_exists($subDir->getRealPath() . '/gulpfile.js')) {
        $themes[$subDir->getBasename()] = $subDir->getRealPath();
      }
    }

    // Execute in sequence to see errors if any.
    $tasks = $this->taskExecStack();

    foreach ($themes ?? [] as $themeName => $themePath) {
      $build = FALSE;
      // Copy cloud code only if
      // - we are inside GITHUB ACTIONS
      // - GITHUB PUSH event has been triggered
      // - we have some file changes at least.
      if (getenv('GITHUB_ACTIONS') == 'true'
        && getenv('GITHUB_EVENT_NAME') == 'push'
        && !empty(getenv('CHANGED_ALL_FILES'))
      ) {
        $themeChanges = getenv('CHANGED_THEME_FILES');
        // Build if theme is changed and tracked in CHANGED_THEME_FILES
        // env variable.
        if (strpos($themeChanges, $themeName) > -1) {
          $build = TRUE;
        }

        // Else copy from acquia repo if build is not needed.
        if ($build === FALSE) {
          $cssFromDir = str_replace('docroot', 'docroot/../deploy/docroot', $themePath);

          // Building folder paths for copying.
          $cssFromDir .= '/css';
          $cssToDir = $themePath . '/css';

          // Copy step.
          $this->say('Copying unchanged ' . $themeName . ' theme from ' . $cssFromDir . ' to ' . $cssToDir);
          $result = $this->taskCopyDir([$cssFromDir => $cssToDir])
            ->overwrite(TRUE)
            ->run();

          // If copying failed preparing for build.
          if (!$result->wasSuccessful()) {
            $this->say('Unable to copy css files from cloud. Building theme ' . $themeName);
            $build = TRUE;
          }
        }
      }
      // Build everything if
      // - we are outside GITHUB ACTIONS
      // - GITHUB create event has been triggered with tag push
      // - it is an empty commit
      //   in merge commit message.
      else {
        $build = TRUE;
      }

      // Build theme css.
      if ($build) {
        $fullBuildCommand = sprintf('cd %s; npm run build', $themePath);
        $tasks->exec($fullBuildCommand);
      }
    }

    $tasks->stopOnFail();
    if ($tasks->getCommand()) {
      $runTasks = $tasks->run();
      $exitCode = $runTasks->getExitCode();
    }
    return $exitCode;
  }

  /**
   * Build styles for specific themes of a particular type.
   *
   * @param string $type
   *   Theme type.
   * @param string $theme
   *   Theme name.
   *
   * @command fe:build-theme
   * @aliases build-theme
   * @description Build styles for all the themes of a particular type.
   */
  public function buildTheme(string $theme) {
    $dir = $this->getConfigValue('docroot') . "/themes/custom/$theme";

    if (!is_dir($dir)) {
      throw new \InvalidArgumentException('Theme not available.');
    }

    $this->taskExec('npm run build')
      ->dir($dir)
      ->run();
  }

  /**
   * Test Theme files.
   *
   * @command fe:test-themes
   * @aliases test-themes
   */
  public function testThemes() {
    $tasks = $this->taskExecStack();
    $tasks->stopOnFail();

    $dir = $this->getConfigValue('docroot') . '/themes/custom';

    foreach (new \DirectoryIterator($dir) as $theme) {
      if ($theme->isDot()
        || (strpos($theme->getBasename(), '.') === 0)
        || in_array($theme->getBasename(), self::$themeIgnoredDirs)
        || !file_exists($theme->getRealPath() . '/gulpfile.js')) {
        continue;
      }

      $theme_dir = $theme->getRealPath();
      $tasks->exec("cd $theme_dir; npm run lint");
    }

    $tasks->stopOnFail();
    return $tasks->run();
  }

  /**
   * Test Theme files.
   *
   * @param string $name
   *   Theme Name.
   *
   * @command fe:test-theme
   * @aliases test-theme
   */
  public function testTheme(string $name) {
    $dir = $this->getConfigValue('docroot') . '/themes/custom/' . $name;

    if (!is_dir($dir)) {
      throw new \InvalidArgumentException($dir . ' does not exist.');
    }

    $tasks = $this->taskExecStack();
    $tasks->stopOnFail();
    $tasks->exec("cd $dir; npm run lint");
    $tasks->stopOnFail();
    return $tasks->run();
  }

}
