mysql -u sbapvr --password=sbapvr -D sbapvr < ..\SQL\creaProcedimenti.sql
mysqldump -u sbapvr --password=sbapvr sbapvr procedimenti > ..\procedimentiweb\procedimenti.sql
php procedimenti.php >> uploadProcedimenti.log