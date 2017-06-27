KilikDbMonitor
==============

A Symfony project created on June 22, 2017, 2:52 pm.

State: work in progress

Install this application
========================

```
git clone git@github.com:KilikFr/DbMonitor.git DbMonitor
cd DbMonitor
composer install
```

Setup this application
======================

```
./bin/console kilik:db-monitor:server:add myserver 127.0.0.1 3306 login password
```

How to scan databases
=====================

```
./bin/console kilik:db-monitor:scan --mode hourly --auto-purge
./bin/console kilik:db-monitor:scan --mode daily --auto-purge
./bin/console kilik:db-monitor:scan --mode monthly --auto-purge
```

Hourly should be executed 1x per hour, daily 1x per day, and monthly 1x per month.
Auto purge automatically delete old records.

Setup automatic scan
====================

copy app/Resources/config/cron.d/kilik-db-monitor to /etc/cron.d/ and fix paths names.
then, service cron reload
