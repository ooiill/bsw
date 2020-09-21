#!/bin/bash

source $(cd `dirname $0`; pwd)/library.sh

if [ `sudo docker images -a | grep 'front' | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build command about front' "\n"
    cat $(cd `dirname $0`; pwd)/dockerfile/front | sudo docker build -t front -
fi

if [ `cat ${profile} | grep "alias ${app}-front" | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for `node`、`npm`、`cnpm`' "\n"
    echo "alias ${app}-front='sudo docker run --privileged=true --rm -v \$(pwd):/app front'" >> ${profile}
    echo "alias ${app}-node='${app}-front node'" >> ${profile}
    echo "alias ${app}-npm='${app}-front npm'" >> ${profile}
    echo "alias ${app}-cnpm='${app}-front cnpm'" >> ${profile}
    color 37 "You can use command as following" "\t" "\n"
    color 37 "\`${app}-node\` substitution \`node\`" "\t" ""
    color 37 "\`${app}-npm\` substitution \`npm\`" "\t" ""
    color 37 "\`${app}-cnpm\` substitution \`cnpm\`"
fi

# -- eof --