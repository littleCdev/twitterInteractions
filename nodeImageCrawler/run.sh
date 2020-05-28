#!/bin/bash

notifyurl=""
servicename="nodeImageCrawler"
logfile="../${servicename}.log"
npm install

while :; do
    node crawlImages.js &>> $logfile

    # $? = crtl+c in crawlImages.js -> normal exit
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


