#!/bin/bash

if [ -f ~/.bash_aliases ]; then
  shopt -s expand_aliases
  source ~/.bash_aliases
fi

ddev composer install
ddev sb setup --host=https://$(basename "$(pwd)").$(ddev config global | grep project-tld | awk -F '=' '{print $2}') --dbhost=db --dbname=db --dbuser=root --dbpassword=root --dbprefix=sb_
