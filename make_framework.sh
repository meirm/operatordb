#!/bin/bash

FRAMEWORK_DIR=/Library/Frameworks/operatordb.framework

rm -rf "${FRAMEWORK_DIR}"
mkdir -p "${FRAMEWORK_DIR}/Versions/A/Headers"
mkdir -p "${FRAMEWORK_DIR}/Versions/A/Resources"
cp Info.plist "${FRAMEWORK_DIR}/Versions/A/Resources/Info.plist"
cp VERSION "${FRAMEWORK_DIR}/Versions/A/Resources/VERSION"
cp operatordb.h "${FRAMEWORK_DIR}/Versions/A/Headers/operatordb.h"
cp liboperatordb.dylib "${FRAMEWORK_DIR}/Versions/A/operatordb"

cd "${FRAMEWORK_DIR}"
ln -s Versions/Current/Headers Headers
ln -s Versions/Current/Resources Resources
ln -s Versions/Current/operatordb operatordb

cd "${FRAMEWORK_DIR}/Versions"
ln -s A Current
cd ..
