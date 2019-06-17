#!/bin/sh

# Build script
# Automate build -> git push 

COMMIT_MESSAGE=""
if [ "$1" == "" ]; then
    echo "\n\nOops. Need parameter 1: Git repo name\n\n"
    exit
fi

if [ "$2" == "" ]; then
    echo "\n\nParameter 2: Commit message not given, defaulting to 'automated build'\n\n"
    COMMIT_MESSAGE='automated build'
else 
    COMMIT_MESSAGE=$2
fi

REPO_NAME=$1

BUILD_OUTPUT=$(docker build -t chubbycat/lampodbc . | tail -3)
if [ "$?" -ne "0" ]; then
  echo "\n\nSorry the build failed with the following message:\n"
  echo $BUILD_OUTPUT 
  exit 1
fi

echo "\n\nBuild successful\nDocker returned the following line(s):\n"
echo "$BUILD_OUTPUT\n\n"

GIT_BRANCH=$(git branch | grep \* | cut -d ' ' -f2)
echo "Git Repo: $REPO_NAME branch: $GIT_BRANCH"

OUTPUT=$(git add . | tail -3)
if [ "$?" -ne "0" ]; then
  echo "\n\nSorry, git add failed with the following message:\n"
  echo $OUTPUT 
  exit 1
fi
echo "... added done"

OUTPUT=$(git commit -m "$COMMIT_MESSAGE" | tail -3)
if [ "$?" -ne "0" ]; then
  echo "\n\nSorry, git commit -m \"$COMMIT_MESSAGE\""" failed with the following message:\n"
  echo $OUTPUT 
  exit 1
fi
echo "... commit done"

OUTPUT=$(git push $REPO_NAME $GIT_BRANCH | tail -3)
if [ "$?" -ne "0" ]; then
  echo "\n\nSorry, git push to $REPO_NAME $GIT_BRANCH failed with the following message:\n"
  echo $OUTPUT 
  exit 1
fi
echo "... push done"

echo "\n\n";

