#!/bin/bash

# Path to your instance without trailing slash
rootDir=/var/www/legacy-rp-admin-v3

# $1 is the cluster (c3, c2, etc.)
# $2 is the dev-token
runCronJobs() {
    echo -n "Running jobs for $1..."
    echo -n $(curl -s https://$1.legacy-roleplay.com/cron/bans?token=$2)"..."
    echo $(curl -s https://$1.legacy-roleplay.com/cron/economy?token=$2)
}

# $1 is the directory
countFiles() {
    ls $1 -1 | wc -l
}

# For each of your instances copy this
runCronJobs "c3" "my-dev-token"


# Automatically delete log files older than 14 days
before=$(countFiles $rootDir/storage/logs/)

find $rootDir/storage/logs/ -mindepth 1 -type f -mtime +14 -delete

after=$(countFiles $rootDir/storage/logs/)
echo "Removed "$(($before - $after))" log files"

# Automatically delete sessions older than 2 days
before=$(countFiles $rootDir/storage/framework/session_storage)

find $rootDir/storage/framework/session_storage -mindepth 1 -type f -mtime +2 -delete

after=$(countFiles $rootDir/storage/framework/session_storage)
echo "Removed "$(($before - $after))" .session files"
