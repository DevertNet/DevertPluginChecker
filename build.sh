#!/bin/bash

DIR=$(dirname $0) ;
cd $DIR;
DIR=$(pwd);

PLUGIN_NAME=$1
if [ -z "$PLUGIN_NAME" ]; then
    echo "Parameter missing. Usage: Fire ./build.sh YourPluginName"
    exit;
fi

rm -rf $DIR/dist
mkdir $DIR/dist
mkdir $DIR/dist/$PLUGIN_NAME

cp -R $DIR/src $DIR/dist/$PLUGIN_NAME/src
cp -R $DIR/CHANGELOG_de-DE.md $DIR/dist/$PLUGIN_NAME/CHANGELOG_de-DE.md
cp -R $DIR/CHANGELOG.md $DIR/dist/$PLUGIN_NAME/CHANGELOG.md
cp -R $DIR/composer.json $DIR/dist/$PLUGIN_NAME/composer.json

cd $DIR/dist
zip -r $DIR/dist/$PLUGIN_NAME.zip ./$PLUGIN_NAME