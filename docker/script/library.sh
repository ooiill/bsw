#!/bin/bash

function color()
{
    color=$1
    message=$2
    begin=${3-"\t"}
    end=${4-"\n"}

    echo -e "${begin}\033[1;${color}m${message}\033[0;0m${end}"
}

function newlyAlias()
{
    profile=${1}
    local var=$(declare -p "${2}")
    eval "declare -A local ref"=${var#*=}
    classify=${3-alias}

    for key in ${!ref[*]};
    do
        if [ `cat ${profile} | grep "${classify} ${key}=" | wc -l | awk '{print $1}'` == 0 ]; then
            echo "${classify} ${key}=\"${ref[${key}]}\"" >> ${profile}
            color 37 "Newly ${classify} for ${key}" "\t" ""
        fi
    done
}

# -- end of func --

app=${1-bsw-app}
project=${2-/web/app}
profile=~/.bash_profile

# create profile
if [ ! -f ${profile} ]; then
    color 34 "Touch file '${profile}'" "\n\t"
    touch ${profile}
fi

declare -A aliasList=(
    ["iload"]="source ~/.bash_profile"
    ["ivim"]="sudo vim ~/.bash_profile"
    ["igrep"]="cat ~/.bash_profile | grep \${1}"
    ["hvim"]="sudo vim /etc/hosts"
    ["hgrep"]="cat /etc/hosts | grep \${1}"
    ["ip1"]="ifconfig | grep -E '([0-9]{1,3}\.){3}[0-9]{1,3}' | grep -v 127.0.0.1 | awk '{ print \$2 }' | cut -f2 -d: | head -n1"
    ["ip2"]="curl ip.sb"
    ["iptab.cnf"]="sudo vim /etc/sysconfig/iptables"
    ["iptab.reload"]="sudo systemctl restart iptables"
    ["dc.dev"]="sudo docker-compose -f dev.yml"
    ["dc.beta"]="sudo docker-compose -f beta.yml"
    ["dc.prod"]="sudo docker-compose -f prod.yml"
)

color 34 "Newly alias for basic in ${profile}" "\n"
newlyAlias ${profile} aliasList alias

declare -A exportList=(
    ["GOODESS1"]="\$(ip route | awk '/default/ { print \$3 }')"
    ["GODDESS2"]="\$(ifconfig | grep -E '([0-9]{1,3}\.){3}[0-9]{1,3}' | grep -v 127.0.0.1 | awk '{ print \$2 }' | cut -f2 -d: | head -n1)"
)

color 34 "Newly export for basic in ${profile}" "\n"
newlyAlias ${profile} exportList export

# alias for docker0 rebuild
if [ `cat ${profile} | grep 'function docker\.rebuild-network-card' | wc -l | awk '{print $1}'` == 0 ]; then
    (
cat <<'EOF'
function docker.rebuild-network-card()
{
    sudo apt install -y bridge-utils
    sudo pkill docker
    sudo iptables -t nat -F
    sudo ifconfig docker0 down
    sudo brctl delbr docker0
    sudo service docker restart
}
EOF
) >> ${profile}
fi

soft="fpm nginx redis mysql"
declare -A dockerListA
for key in ${soft};
do
    dockerListA["${app}-id-${key}"]="sudo docker ps -a --format='table {{.ID}}\t{{.Names}}' | grep '_${app}-${key}' | awk '{print \\\$1}'"
done
color 34 "Newly alias for docker in ${profile}" "\n"
newlyAlias ${profile} dockerListA alias

declare -A dockerListB
for key in ${soft};
do
    dockerListB["${app}-logs-${key}"]="sudo docker logs \$(${app}-id-${key})"
    dockerListB["${app}-container-${key}"]="sudo docker exec --privileged=true -it \$(${app}-id-${key})"
    dockerListB["${app}-container-${key}-cli"]="sudo docker exec --privileged=true -i \$(${app}-id-${key})"
done

color 34 "Newly alias for docker in ${profile}" "\n"
newlyAlias ${profile} dockerListB alias

# -- eof --