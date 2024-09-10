#!/bin/bash

# Start cron
service cron start 

# Start supervisord
/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
