#!/bin/bash

notifyurl=""
servicename="phpCreateImage"

while :; do
    php index.php;

    if [ $? -gt 0 ]
    then
        if [ ! -z "$notifyurl" ]
        then
            wget -4 --quiet --no-check-certificate -O /dev/null "${notifyurl}/message/${servicename} exited, restarting...";
        fi;
        sleep 60;
    fi
    sleep 3;
done
