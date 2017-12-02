#!/bin/bash

# Define the color scheme.
FG_C='\033[1;37m'
BG_C='\033[42m'
WBG_C='\033[43m'
EBG_C='\033[41m'
NO_C='\033[0m'

export TERMINUS_SITE=contentacms;
export ADMIN_EMAIL='bot@contentacms.org';
export ADMIN_PASSWORD='Ah9!6gpqdZu1n^%4WJM*4';
export INSTALL_PATH=/Users/e0ipso/workspace/contenta_jsonapi_demo
export CURRENT_PATH=$(pwd)

cd $INSTALL_PATH;
# Make sure to bring the latest changes from the distro.
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} rm composer.lock"
rm composer.lock;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} rm -fr vendor/"
rm -fr vendor/;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} composer update contentacms/contenta_jsonapi"
composer update contentacms/contenta_jsonapi;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} git add composer.lock vendor/composer/installed.json"
git add composer.lock vendor/composer/installed.json web/profiles/contrib/contenta_jsonapi;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} git commit -m 'Update installed contenta distro'"
git commit -m 'Update installed contenta distro';

# Build the assets and push them to Pantheon.
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} terminus auth:login"
terminus auth:login;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} terminus env:wake -n $TERMINUS_SITE.dev"
terminus env:wake -n "$TERMINUS_SITE.dev";
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} composer -n build-assets"
composer -n build-assets;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} rm -fr config"
rm -fr config;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} cp -r web/profiles/contrib/contenta_jsonapi/config/sync config"
cp -r web/profiles/contrib/contenta_jsonapi/config/sync config;
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} terminus build:env:push -n "$TERMINUS_SITE.dev" --yes"
terminus build:env:push -n "$TERMINUS_SITE.dev" --yes
# Install the site with the newly built assets.
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} terminus build:env:install -n "$TERMINUS_SITE.dev" --account-mail="$ADMIN_EMAIL" --account-pass="[REDACTED]" --site-name="Contenta CMS" --site-mail="$ADMIN_EMAIL" -v"
terminus build:env:install -n "$TERMINUS_SITE.dev" --account-mail="$ADMIN_EMAIL" --account-pass="$ADMIN_PASSWORD" --site-name="Contenta CMS" --site-mail="$ADMIN_EMAIL" -v;
# Do some cleanup.
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} terminus build:env:delete:pr -n "$TERMINUS_SITE" --yes"
terminus build:env:delete:pr -n "$TERMINUS_SITE" --yes;

cd $CURRENT_PATH;
say "Installation finished";

echo -e "${WBG_C}${BG_C} NOTE ${NO_C} Consider pushing: 'git push origin master'";
