# New Sub Theme Setup

## Changes needed after copying

* Rename the directory.
* Rename `.info` file and the name of theme and description in info file.
* Rename `.libraries.yml` file.
* Rename `.theme` file.
* Rename `.breakpoints.yml` file.
* Rename the `.settings.yml` file in `config/install`.
* Create a symlink for `package.json` file from the transac directory and place it inside the root of your new theme.
* Without the package.json symlink the build commands wont work for this theme.

## After renaming

* change the `screenshot.png`, `logo.svg` and `favicon.ico` as per the
requirements.

## Colors & Theme inheritance

* We use color variables which can be overriden inside brand themes inside
`_colors.scss`
* This way without writing any CSS you can override colors for components coming
 from base theme.
* Maximum emphasis should be given to reuse from base theme and override what is
 necessary rather than duplicating.

## Fonts & Theme inheritance.

* Fonts are declared under conditional-sass, we add all font faces here.
* Font variables are inside sass/config/_fonts.scss.
* A library called font-face is attached to global styles in base theme, this library loads all font faces.
* This is subsequently overridden in child themes, to have a clean override system and avoid unnecessary font loading.

## Usage and development

* For rebuilding sass, run `npm run build` from your theme directory.
