#!/bin/sh

ENV_JSON=/var/www/env.json

printenv | jq --raw-input --slurp \
    'split("\n")[:-1] | map(split("=")) | map_values ( { (.[0]) : (.[1:]) | join("=") } ) | add' \
    > $ENV_JSON && \
    chmod 400 $ENV_JSON

#env | sed "s/\(.*\)=\(.*\)/export \1='\2'/" >> /etc/apache2/envvars

echo "clear_env = no" >> /etc/php/7.0/fpm/pool.d/www.conf
env | sed "s/\(.*\)=\(.*\)/env[\1]='\2'/" >> /etc/php/7.0/fpm/pool.d/www.conf

/etc/init.d/ssh start
apache2ctl -k start -DFOREGROUND

#/var/www/code-server/code-server /var/www

