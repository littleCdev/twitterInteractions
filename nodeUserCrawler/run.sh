#!/bin/bash

notifyurl=""
servicename="nodeUserCrawler"
logfile="../${servicename}.log"

while :; do
    node userCrawler.js &>> $logfile

    # $? = crtl+c in userCrawler.js -> normal exit
    if [ $? -eq 0 ]
    then
     exit 0;
    fi

     if [ ! -z "$notifyurl" ]
    then
        wget -4 --quiet --no-check-certificate -O /dev/null "${notifyurl}/message/${servicename} exited, restarting...";
    fi;

    sleep 30;
done


