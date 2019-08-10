#!/bin/bash

wget mods.gb-hoster.me/Games/CounterStrike/Public.zip
unzip Public.zip
rm -rf Public.zip
rm -rf script.sh
rm -rf wget-log
chmod -R 755 *