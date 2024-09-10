#!/bin/bash

# Start cron
service cron restart 

# Start supervisord
/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
