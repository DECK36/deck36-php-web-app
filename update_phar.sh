#!/bin/bash


./box.phar add -r deck36-plan9-storm.phar app/config/config.yml	app/config/config.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/config_prod.yml	app/config/config_prod.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/parameters.yml	app/config/parameters.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/routing_dev.yml	app/config/routing_dev.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/storm.yml	app/config/storm.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/storm_prod.yml app/config/storm_prod.yml
./box.phar add -r deck36-plan9-storm.phar app/config/config_dev.yml	app/config/config_dev.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/config_test.yml	app/config/config_test.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/routing.yml	app/config/routing.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/security.yml	app/config/security.yml	
./box.phar add -r deck36-plan9-storm.phar app/config/storm_dev.yml app/config/storm_dev.yml

./box.phar add -r deck36-plan9-storm.phar src/Deck36/Bundle/StormBundle/Command/StatusLevelBolt.php src/Deck36/Bundle/StormBundle/Command/StatusLevelBolt.php
./box.phar add -r deck36-plan9-storm.phar src/Deck36/Bundle/StormBundle/Command/PrimeCatBolt.php src/Deck36/Bundle/StormBundle/Command/PrimeCatBolt.php
