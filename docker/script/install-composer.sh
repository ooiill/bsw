#!/bin/bash

source $(cd `dirname $0`; pwd)/library.sh

if [ `sudo docker images -a | grep 'composer' | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Pull library/composer:latest' "\n"
    sudo docker pull library/composer:latest
fi

if [ `cat ${profile} | grep "alias ${app}-composer" | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for `composer`' "\n"
    # library/composer php -d memory_limit=-1 /usr/bin/composer
    echo "alias ${app}-composer='sudo docker run --privileged=true --rm -v \$(pwd):/app -v ~/.ssh/id_rsa.pub:/root/.ssh/id_rsa.pub -v ~/.ssh/id_rsa:/root/.ssh/id_rsa -v ~/.ssh/known_hosts:/root/.ssh/known_hosts --add-host git.app.com:103.1.2.3 library/composer composer'" >> ${profile}
    color 37 "You can use command \`${app}-composer\` substitution \`composer\` now"
fi

# -- eof --