#!/bin/sh

ENV_JSON=/var/www/env.json
if [ -z ${PORT} ]
then 
    echo "\nENV PORT was not set, defaulting to DEFAULT_LISTEN_PORT_HTTP(${DEFAULT_LISTEN_PORT_HTTP})...\n";
    PORT=${DEFAULT_LISTEN_PORT_HTTP}; 
fi;

echo "\nPreparing Apache envars...\n"
printenv | jq --raw-input --slurp \
    'split("\n")[:-1] | map(split("=")) | map_values ( { (.[0]) : (.[1:]) | join("=") } ) | add' \
    > $ENV_JSON && \
    chmod 444 $ENV_JSON

env | sed "s/\(.*\)=\(.*\)/export \1='\2'/" >> /etc/apache2/envvars


#echo "clear_env = no" >> /etc/php/7.0/fpm/pool.d/www.conf
#env | sed "s/\(.*\)=\(.*\)/env[\1]='\2'/" >> /etc/php/7.0/fpm/pool.d/www.conf

echo "\nStasting sshd...\n"
/etc/init.d/ssh start

echo "\nStarting Apache in the foreground, listening to port ${PORT}...\n"
apache2ctl -k start -DFOREGROUND

#/var/www/code-server/code-server /var/www

