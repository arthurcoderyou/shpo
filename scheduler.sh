#!/bin/bash

while true; do
    php artisan schedule:run >> storage/logs/scheduler.log 2>&1
    sleep 60
done