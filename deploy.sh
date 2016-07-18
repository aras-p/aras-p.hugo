#!/bin/sh
cd $(dirname "$0")
rm -rf public
./hugo
cp public/index.xml public/atom.xml
rsync -az --force --progress -e "ssh" public/ nearaz@aras-p.info:~/aras-p.info/
rm -rf public
