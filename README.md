# KerzenziehenKasse
Kasse für Kerzenziehen

## Setup
### XAMPP
 1. Install https://www.apachefriends.org/de/index.html
 1. Run the config tool as root and make sure `apache` and `mysql` are automatically run as services, see http://www.sintesisdigital.com.mx/dashboard/docs/auto-start-xampp.html.
 1. Config changes:
   - enable `short_open_tag` in `php.ini`, see https://stackoverflow.com/questions/5603898/php-file-with-tags-in-xampp
   - Optional allow `phpmyadmin` acccess from external, see https://www.apachefriends.org/faq_windows.html but also set a password, see https://www.thewindowsclub.com/how-to-change-phpmyadmin-password-on-xampp
 1. Clone the git repo https://github.com/caco3/KerzenziehenKasse to `D:\xampp\htdocs\kerzenziehen`.


### Kasse
 1. Copy `src/config/config.php.template` to `src/config/config.php` and update its content as needed.
 1. Point your webbrowser to `src/`.

### DB-Backup
 1. Copy `database_backup.bat.bat.template` to `database_backup.bat.bat` and update its content as needed.
 1. Setup a task to run it periodically. Use `Taskschd.msc` and see https://techrando.com/2019/06/22/how-to-execute-a-task-hourly-in-task-scheduler/ 