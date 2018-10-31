#!/usr/bin/env bash
cd /home/prasys/www/sbapveronaws/scripts
phpbin=$(which php)
scriptpath=$(pwd)
sudo mount -a
if [ ! -e "/tmp/batchPecLoad.pid" ]; then
    if [ ! -e "/home/prasys/www/pecmail/NON_CANCELLARE.TXT" ]; then
        echo "[`date`] : importazionePec.sh : il nas non è montato!" >> /home/prasys/www/logs/importazionePec.log
    else

        touch /tmp/batchPecLoad.pid
        $phpbin $scriptpath/batchPecMail.php >> /home/prasys/www/logs/importazionePec.log
        # $phpbin $scriptpath/batchPecMail.php MAIL >> /home/prasys/www/logs/importazionePec.log

        $phpbin $scriptpath/RicevuteConsegnaAccettazione.php >> /home/prasys/www/logs/RicevuteConsegnaAccettazione.log

        rm /tmp/batchPecLoad.pid
    fi
else
    echo "[`date`] : batchPecLoad.sh : Process is already running" >> /home/prasys/www/logs/importazionePec.log
fi
