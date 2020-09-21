#!/bin/bash

source $(cd `dirname $0`; pwd)/library.sh

#
# Install command `php`
#
if [ `cat ${profile} | grep "alias ${app}-mysql" | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for run `mysql`' "\n"
    echo "alias ${app}-mysql='${app}-container-mysql mysql'" >> ${profile}
    color 37 "You can use command \`${app}-mysql\` substitution \`mysql\` now"  "\t" ""
fi

if [ `cat ${profile} | grep "alias ${app}-mysqldump" | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for run `mysqldump`' "\n"
    echo "alias ${app}-mysqldump='${app}-container-mysql mysqldump'" >> ${profile}
    color 37 "You can use command \`${app}-mysqldump\` substitution \`mysqldump\` now" "\t" ""
fi

if [ `cat ${profile} | grep "alias ${app}-mysqlbinlog" | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for run `mysqlbinlog`' "\n"
    echo "alias ${app}-mysqlbinlog='${app}-container-mysql mysqlbinlog'" >> ${profile}
    color 37 "You can use command \`${app}-mysqlbinlog\` substitution \`mysqlbinlog\` now"
fi

# -- eof --