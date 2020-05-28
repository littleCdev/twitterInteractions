#!/bin/bash

notifyurl=""
servicename="nodeLikeBot"
logfile="../${servicename}.log"
npm install

while :; do
    node likeBot.js 2>&1 | tee $logfile

     if [ ! -z "$notifyurl" ]
    then
        wget -4 --quiet --no-check-certificate -O /dev/null "${notifyurl}/message/${servicename} exited, restarting...";
    fi;

    sleep 30;
done


