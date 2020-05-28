#!/bin/bash

databaseserver=""
notifyurl=""

if [ ! -z "$notifyurl" ]
then
    wget -4 --quiet --no-check-certificate -O /dev/null "${notifyurl}/message/Reboot! Starting all..";
fi;


echo "waiting for Server"
while ! ping -c 1 -n -w 1 $databaseserver &> /dev/null
do
    printf "%c" "."
done
echo "server online, staring scripts";

tmux new-session -d -s "nodeImageCrawler" "cd nodeImageCrawler;bash run.sh";
tmux new-session -d -s "nodeUserCrawler" "cd nodeUserCrawler;bash run.sh";
tmux new-session -d -s "phpCreateImage" "cd phpCreateImage;bash run.sh";
tmux new-session -d -s "nodeLikeBot" "cd nodeLikeBot;bash run.sh";