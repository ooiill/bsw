#!/usr/bin/env bash

user=${1}
if [ -z "${user}" ]; then
    user=`whoami`
fi

shopt -s expand_aliases
. /home/${user}/.bash_profile > /dev/null 2>&1
. /${user}/.bash_profile > /dev/null 2>&1

app=$(cd `dirname ${0}`; pwd)/../
cd ${app}

bsw-app-sf-bin-cli xxx
