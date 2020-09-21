#!/bin/bash

source $(cd `dirname $0`; pwd)/library.sh

#
# Install command `bin/console`
#
if [ `cat ${profile} | grep "alias ${app}-sf-bin" | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for `php bin/console`' "\n"
    echo "alias ${app}-sf-bin='${app}-container-fpm php ${project}/bin/console'" >> ${profile}
    echo "alias ${app}-sf-bin-cli='${app}-container-fpm-cli php ${project}/bin/console'" >> ${profile}
    color 37 "You can use command \`${app}-sf-bin\` substitution \`php bin/console\` now"
fi

#
# Install command `bin/phpunit`
#
if [ `cat ${profile} | grep 'alias ${app}-sf-unit' | wc -l | awk '{print $1}'` == 0 ]; then
    color 34 'Build alias for `php bin/phpunit`' "\n"
    echo "alias ${app}-sf-unit='${app}-container-fpm php ${project}/bin/phpunit'" >> ${profile}
    color 37 "You can use command \`${app}-sf-unit\` substitution \`php bin/phpunit\` now"
fi

declare -A aliasList=(
    ["bin.${app}-bsw"]="${app}-sf-bin bsw:init --force=yes --document-need=no --config-need=no --app=backend --scaffold-path=${project}/vendor/ooiill/bsw-bundle --scaffold-ns=Leon\\\\\\\\BswBundle"
    ["bin.${app}-api"]="${app}-sf-bin bsw:init --force=yes --document-need=no --config-need=no --app=api --scheme-bsw=no --scheme-extra=${project}/src/Module/Scheme"
    ["bin.${app}-web"]="${app}-sf-bin bsw:init --force=yes --document-need=no --config-need=no --app=web --scheme-bsw=no --scheme-extra=${project}/src/Module/Scheme"
    ["bin.${app}-backend"]="${app}-sf-bin bsw:init --force=yes --document-need=no --config-need=no --app=backend --scheme-bsw=no --scheme-extra=${project}/src/Module/Scheme"
)

color 34 "Newly alias for symfony in ${profile}" "\n"
newlyAlias ${profile} aliasList alias

color 44 " Script install done, You should run the command manually as following " "\n\t"
color 34 "source ${profile}" "\tuser@bsw:~ "

# -- eof --