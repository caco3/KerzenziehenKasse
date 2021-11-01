# KerzenziehenKasse
Kasse für Kerzenziehen

## Setup
 1. Copy `src/config/config.php.template` to `src/config/config.php` and update its content as needed.
 1. Point your webbrowser to `src/`.

### DB-Backup
 1. Copy `database_backup.bat.bat.template` to `database_backup.bat.bat` and update its content as needed.
 1. Setup a task to run it periodically. Use following cmd line code to register it:  `schtasks /create /tn "DB-Backup" /tr "C:\xampp\htdocs\kerzenziehen\database_backup.bat" /sc minute /mo 60`. Use `Taskschd.msc` to check it and see https://techrando.com/2019/06/22/how-to-execute-a-task-hourly-in-task-scheduler/ 

