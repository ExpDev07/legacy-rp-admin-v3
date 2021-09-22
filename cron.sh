#!/bin/bash

# $1 is the cluster (c3, c2, etc.)
# $2 is the dev-token
runCronJobs() {
    echo -n "Running jobs for $1..."
    echo -n $(curl -s https://$1.legacy-roleplay.com/cron/bans?token=$2)"..."
    echo $(curl -s https://$1.legacy-roleplay.com/cron/economy?token=$2)
}

# For each of your instances copy this
runCronJobs "c3" "my-dev-token"
