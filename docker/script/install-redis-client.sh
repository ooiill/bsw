#!/bin/bash

source $(cd `dirname $0`; pwd)/library.sh

#
# Install command `php`
#
if [ `cat ${profile} | grep "alias ${app}-redis" | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for run `redis`' "\n"
    echo "alias ${app}-redis='${app}-container-redis redis-cli'" >> ${profile}
    color 37 "You can use command \`${app}-redis\` substitution \`redis-cli\` now"
fi

# -- eof --