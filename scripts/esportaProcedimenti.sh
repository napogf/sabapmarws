#!/bin/bash
cd /home/prasys/www/sbapveronaws/scripts
mysql -u sbapvrws --password=sbapvrws sbapvrws < ../SQL/creaProcedimenti.sql
mysqldump -u sbapvrws --password=sbapvrws --no-create-info sbapvrws procedimenti  > ../procedimentiweb/arc_procedimenti.sql
scp -P 7750 ../procedimentiweb/arc_procedimenti.sql sabapvr@151.13.7.88:/home/sabapvr/sites/sbapvr/docker/mysql/docker-entrypoint-initdb.d/arc_procedimenti.sql
rm ../procedimentiweb/*.sql
php procedimenti.php >> ../logs/uploadProcedimenti.log
scp -P 7750 ../procedimentiweb/*.pdf sabapvr@151.13.7.88:/home/sabapvr/sites/sbapvr/procedimenti/files/
rm ../procedimentiweb/*.pdf
