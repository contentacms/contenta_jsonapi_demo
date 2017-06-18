<!-- @file Instructions for subtheming using the Sass Starterkit. -->
<!-- @defgroup sub_theming_sass -->
<!-- @ingroup sub_theming -->
# Sass Starterkit

Below are instructions on how to create a Materialize sub-theme using a LESS
preprocessor.

- [Prerequisites](#prerequisites)
- [Additional Setup](#setup)
- [Override Styles](#styles)
- [Override Settings](#settings)
- [Override Templates and Theme Functions](#registry)


BUILD A THEME WITH MATERIALIZE
------------------------------

The base materialize theme is designed to be easily extended by its sub-themes. You
shouldn't modify any of the CSS or PHP files in the materialize/ folder; but instead you
should create a sub-theme of materialize which is located in a folder outside of the
root materialize/ folder. The examples below assume materialize and your sub-theme will be
installed in themes/, but any valid theme directory is acceptable (read
the sites/example.sites.php for more info.)

This description based on the Zen theme's starter-kit Readme.txt. https://www.drupal.org/node/2767991

*** IMPORTANT NOTE ***
*
* In Drupal 8, the theme system caches which template files and which theme
* functions should be called. This means that if you add a new theme,
* preprocess function to your [SUB-THEME].theme file or add a new template
* (.twig) file to your sub-theme, you will need to rebuild the "theme registry."
* See https://drupal.org/node/173880#theme-registry
*
* Drupal 8 also stores a cache of the data in .info.yml files. If you modify any
* lines in your sub-theme's .info.yml file, you MUST refresh Drupal 8's cache by
* simply visiting the Appearance page at admin/appearance.
*

There are 2 ways to create a Materialize sub-theme:
1. An automated way using Drush
2. The manual way


CREATING A SUB-THEME WITH DRUSH
-------------------------------

 1. Install Drush. See https://github.com/drush-ops/drush for details.

 2. Ensure drush knows about the materialize_subtheme command.

    After you have installed Materialize, Drush requires you to enable the Materialize theme
    before using Materialize's Drush commands. To make the drush materialize_subtheme command available
    to use, type:

      drush en materialize -y

 3. See the options available to the drush materialize_subtheme command by typing:

      drush help materialize_subtheme

 4. Create a sub-theme by running the drush materialize_subtheme command with the desired
    parameters. IMPORTANT: The machine name of your sub-theme must start with an
    alphabetic character and can only contain lowercase letters, numbers and
    underscores. Type:

      drush materialize_subtheme [machine_name] [name] [options]

    Here are some examples:
    * Use:  drush materialize_subtheme "Amazing name"
      to create a sub-theme named "Amazing name" with a machine name
      (automatically determined) of amazing_name, using the default options.
    * Use:  drush materialize_subtheme momg_amazing "Amazing name"
      to create a sub-theme named "Amazing name" with a machine name of
      momg_amazing, using the default options.
    * Use:  drush materialize_subtheme "Amazing name" --path=sites/default/themes --description="So amazing."
      to create a sub-theme in the specified directory with a custom
      description.

 5. Check your website to see what themes are used as the default and admin
    themes. Type:

      drush status theme

 6. Set your website's default theme to be the new sub-theme. Type:

      drush vset theme_default momg_amazing

      (Replace "momg_amazing" with the actual machine name of your sub-theme.)

 7. Skip to the "ADDITIONAL SETUP" section below to finish creating your
    sub-theme.


CREATING A SUB-THEME MANUALLY
-------------------------------

 1. Setup the location for your new sub-theme.

    Copy the STARTERKIT folder out of the materialize/ folder and rename it to be your
    new sub-theme. IMPORTANT: The name of your sub-theme must start with an
    alphabetic character and can only contain lowercase letters, numbers and
    underscores.

    For example, copy the themes/materialize/STARTERKIT folder and rename it
    as themes/foo.

      Why? Each theme should reside in its own folder. To make it easier to
      upgrade Materialize, sub-themes should reside in a folder separate from the base
      theme.

 2. Setup the basic information for your sub-theme.

    In your new sub-theme folder, rename the STARTERKIT.info.yml file to include
    the name of your new sub-theme. Then edit the .info.yml file by editing the
    name and description field.

    For example, rename the foo/STARTERKIT.info.yml file to foo/foo.info.yml.
    Edit the foo.info.yml file and change "name: Materialize Sub-theme Starter Kit" to
    "name: Foo" and "description = Read..." to "description = A Materialize sub-theme".

      Why? The .info.yml file describes the basic things about your theme: its
      name, description, template regions, and libraries. See the Drupal Theme
      Guide for more info: https://www.drupal.org/documentation/theme

    Remember to visit your site's Appearance page at admin/appearance to refresh
    Drupal 8's cache of .info.yml file data.

 3. Edit your sub-theme to use the proper function names.

    Edit the [SUB-THEME].theme and theme-settings.php files in your sub-theme's
    folder; replace ALL occurrences of "STARTERKIT" with the name of your
    sub-theme.

    For example, edit foo/foo.theme and foo/theme-settings.php and replace
    every occurrence of "STARTERKIT" with "foo".

    It is recommended to use a text editing application with search and
    "replace all" functionality.

 4. Set your website's default theme.

    Log in as an administrator on your Drupal site, go to the Appearance page at
    admin/appearance and click the "Enable and set default" link next to your
    new sub-theme.


ADDITIONAL SETUP
----------------
Your new Materialize sub-theme uses Gulp.js as a task runner, so that it can do many
different tasks automatically:
 - Build your CSS from your Sass using libSass and node-sass.
 - Add vendor prefixes for the browsers you want to support using Autoprefixer.
 - Build a style guide of your components based on the KSS comments in your Sass
   source files.
 - Lint your Sass using sass-lint. // todo
 - Lint your JavaScript using eslint. // todo
 - Watch all of your files as you develop and re-build everything on the fly.

Set up your front-end development build tools:

 1. Install Node.js and npm, the Node.js package manager.

    Detailed instructions are available on the "npm quick start guide":
    https://github.com/kss-node/kss-node/wiki/npm-quick-start-guide

 2. The package.json file in your new sub-theme contains the versions of all the
    Node.js software you need. To install them run:

      npm install

 3. Install the gulp-cli tool globally. Normally, installing a Node.js globally
    is not recommended, which is why both Gulp and Grunt have created wrapper
    commands that will allow you to run "gulp" or "grunt" from anywhere, while
    using the local version of gulp or grunt that is installed in your project.
    To install gulp's global wrapper, run:

      npm install -g gulp-cli

 4. Install the materialize library files to the sub-theme folder

      gulp materialize-install:src

    If you want manually install, the download the latest zip
    (materialize-src-v0.98.1.zip) to the sub-theme folder and unzip it to the
    proper folders (remove the materialize-css dir).

 4b. todo: add this.
    Set the URL used to access the Drupal website under development. Edit your
    gulpfile.js file and change the options.drupalURL setting:

      options.drupalURL = 'http://localhost';

 5. The default gulp task will give you help hints about configured tasks.

 6. // todo: fix lint, style guide, etc.
    The default gulp task will build your CSS, build your style guide, and lint
    your Sass and JavaScript. To run the default gulp task, type:

      gulp styles

    To watch all your files as you develop, type:

      gulp watch

    // todo: check this.
    To better understand the recommended development process for your Zen
    sub-theme, watch the Drupalcon presentation, "Style Guide Driven
    Development: All hail the robot overlords!"
    https://events.drupal.org/losangeles2015/sessions/style-guide-driven-development-all-hail-robot-overlords

Optional steps:

 6. todo: Modify the box component styling.

    The sass/components/box/_box.scss file describes the styles of the "box"
    component. The code comments in that file reiterate the naming conventions
    use in our CSS and also describe how the nested Sass selectors compile into
    CSS.

    Try running "gulp watch", modifying the Sass, and then looking at how the
    style guide page at styleguide/section-components.html is automatically
    updated with the new CSS.

    Now try uncommenting the example ruleset under the "Drupal selectors"
    heading, recompiling the Sass, and then looking at your Drupal site (not the
    style guide) to see how the box component is applying to your sidebar
    blocks.

 7. todo: Choose your preferred page layout method or grid system.

    By default we are using a standard 12 column fluid responsive grid system.
    The grid helps you layout your page in an ordered, easy fashion.
    [Materialize CSS Grid]: http://materializecss.com/grid.html

 8. Modify the markup in Materialize core's template files.

    If you decide you want to modify any of the .html.twig template files in the
    materialize folder, copy them to your sub-theme's folder before making any changes.
    And then rebuild the theme registry.

    For example, copy materialize/templates/system/page.html.twig to subtheme/templates/page.html.twig.

 9. todo: Modify the markup in Drupal's search form.

    Copy the search-block-form.tpl.php template file from the modules/search/
    folder and place it in your sub-theme's template folder. And then rebuild
    the theme registry.

    You can find a full list of Drupal templates that you can override in the
    templates/README.txt file or https://drupal.org/node/190815

      Why? In Drupal 8 theming, if you want to modify a template included by a
      module, you should copy the template file from the classy theme's
      directory to your sub-theme's template directory and then rebuild the
      theme registry. See the Drupal 8 Theme Guide for more info:
      https://drupal.org/node/173880

 10. todo: Further extend your sub-theme.

    Discover further ways to extend your sub-theme by reading Zen's
    documentation online at:
      https://drupal.org/documentation/theme/zen
    and Drupal 8's Theme Guide online at:
      https://drupal.org/theme-guide/8

Credits

Our work based on the [Materialize Framework]: http://materializecss.com/.
Our Drupal theme are based on the develpers work of the [Bootstrap theme] https://www.drupal.org/project/bootstrap
and [Zend Framework] https://www.drupal.org/project/zend

Thanks Guys!

// todo: write documentation.

## Prerequisites

## Additional Setup {#setup}

## Override Styles {#styles}

## Override Theme Settings {#settings}

## Override Templates and Theme Functions {#registry}
Please refer to the @link registry Theme Registry @endlink topic.

[Materialize Framework]: http://materializecss.com/
[Materialize Icons]: https://material.io/icons/
[Materialize Framework Source Files]: https://www.drupal.org/project/materialize
[SASS]: http://sass-lang.com/

