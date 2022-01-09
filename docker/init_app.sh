#!/bin/bash

sleep 10
php /var/www/bin/console about
/usr/bin/supervisord
