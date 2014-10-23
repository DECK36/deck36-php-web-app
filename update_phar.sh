#!/bin/bash


php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/config.yml	app/config/config.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/config_prod.yml	app/config/config_prod.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/parameters.yml	app/config/parameters.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/routing_dev.yml	app/config/routing_dev.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/storm.yml	app/config/storm.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/storm_prod.yml app/config/storm_prod.yml
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/config_dev.yml	app/config/config_dev.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/config_test.yml	app/config/config_test.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/routing.yml	app/config/routing.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/security.yml	app/config/security.yml	
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar app/config/storm_dev.yml app/config/storm_dev.yml

php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar src/Deck36/Bundle/StormBundle/Command/StatusLevelBolt.php src/Deck36/Bundle/StormBundle/Command/StatusLevelBolt.php
php -d phar.readonly=Off ./box.phar add -r target/resources/deck36-plan9-storm.phar src/Deck36/Bundle/StormBundle/Command/PrimeCatBolt.php src/Deck36/Bundle/StormBundle/Command/PrimeCatBolt.php
