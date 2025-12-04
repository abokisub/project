#!/bin/bash
# Quick script to check Laravel error log
# Run this in cPanel Terminal or SSH

echo "=== Laravel Error Log (Last 50 lines) ==="
echo ""

if [ -f "storage/logs/laravel.log" ]; then
    tail -n 50 storage/logs/laravel.log
else
    echo "ERROR: storage/logs/laravel.log not found!"
    echo "Checking if storage directory exists..."
    ls -la storage/ 2>/dev/null || echo "Storage directory doesn't exist!"
fi

echo ""
echo "=== Checking for PHP Errors ==="
if [ -f "storage/logs/laravel.log" ]; then
    echo "Recent PHP errors:"
    grep -i "error\|exception\|fatal" storage/logs/laravel.log | tail -n 10
fi

