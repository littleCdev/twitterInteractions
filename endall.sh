#!/bin/bash
tmux kill-session -t "nodeImageCrawler"
tmux kill-session -t "nodeUserCrawler"
tmux kill-session -t "phpCreateImage"
tmux kill-session -t "nodeLikeBot"
