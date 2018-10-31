mysql -u sbapvr --password=sbapvr -D sbapvr < dropProcedimenti.sql
mysqldump -u sbapvr --password=sbapvr sbapvr procedimenti > ..\procedimentiweb\procedimenti.sql
php procedimenti.php >> uploadProcedimenti.log
