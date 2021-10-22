@echo off

cls 

echo -------------------------------------------------------
echo RUNNING PHPSTAN ANALYSIS
echo -------------------------------------------------------

echo.

call ../vendor/bin/phpstan analyse -c ./config/phpstan.neon -l 8 > phpstan/output.txt

start "" "phpstan/output.txt"
