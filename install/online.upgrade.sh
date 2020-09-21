#!/usr/bin/env bash

shopt -s expand_aliases
. /home/$(whoami)/.bash_profile > /dev/null 2>&1

basic=$(cd `dirname $0`; pwd)
dir="${basic}/../.."

bsw=${dir}/php-vendor/bsw/

echo Update ooiill/bsw-bundle
(cd ${bsw} && git pull)

sudo chmod a+w -R public/bundles/
echo
echo Update current
git checkout .
git pull
echo APP_ENV=${1-prod} > .env

bsw-app-sf-bin assets:install
(
    cd public/bundles/leonbsw/
    bsw-app-npm install
    bsw-app-npm run all
)

bsw-app-sf-bin cache:clear
sudo chmod -R 777 var/
