#!/bin/bash

LOG_FILE=log/script_runner.log

while getopts r:b:u:m:c: option
do
        case "${option}"
        in
                r) REPOSITORY=${OPTARG};;
                b) BRANCH=${OPTARG};;
                u) USER=${OPTARG};;
                m) MESSAGE=${OPTARG};;
				c) COMMITID=${OPTARG};;
        esac
done


echo "REPOSITORY=$REPOSITORY" >> $LOG_FILE
echo "BRANCH=$BRANCH" >> $LOG_FILE
echo "USER=$USER" >> $LOG_FILE
echo "MESSAGE=$MESSAGE" >> $LOG_FILE
echo "COMMITID=$COMMITID" >> $LOG_FILE

# ==============================================
# Do your worst below here...
# ==============================================

echo "Running ./hooks/$REPOSITORY.sh -c '$COMMITID' -b '$BRANCH' -u '$USER' -m '$MESSAGE'"
./hooks/$REPOSITORY.sh -c "$COMMITID" -b "$BRANCH" -u "$USER" -m "$MESSAGE"
