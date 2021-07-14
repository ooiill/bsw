#!/usr/bin/env bash

bsw=${1}
basic=$(cd `dirname $0`; pwd)

if [ "${bsw}" == "" ]; then
    bsw=$(cd ${basic}/../../bsw-bundle/; pwd)
fi

if [ ! -d "${bsw}" ]; then
    echo -e "\n\t\033[1;31mPath of bsw is non-exists\033[0;0m\n"
    exit 104
fi

vendor=$(cd ${basic}/../vendor/ooiill/bsw-bundle; pwd)
if [ ! -d "${vendor}" ]; then
    echo -e "\n\t\033[1;31mPath of vendor is non-exists\033[0;0m\n"
    exit 104
fi

rm -rf ${vendor} && ln -s ${bsw} ${vendor}