#!/usr/bin/env bash
rootdir="/var/www"
cd /var/www/scripts
phpbin=$(which php)
scriptpath=$(pwd)

sudo mount -a
if [ ! -e "/tmp/batchPecLoad.pid" ]; then
#    if [ ! -e "$rootdir/pecmail/NON_CANCELLARE.TXT" ]; then
#        echo "[`date`] : importazionePec.sh : il nas non è montato!" >> $rootdir/logs/importazionePec.log
#    else

        touch /tmp/batchPecLoad.pid
        $phpbin $scriptpath/batchPecMail.php >> $rootdir/logs/importazionePec.log
        # $phpbin $scriptpath/batchPecMail.php MAIL >> $rootdir/logs/importazionePec.log

        $phpbin $scriptpath/RicevuteConsegnaAccettazione.php >> $rootdir/logs/RicevuteConsegnaAccettazione.log

        rm /tmp/batchPecLoad.pid
#    fi
else
    echo "[`date`] : batchPecLoad.sh : Process is already running" >> $rootdir/logs/importazionePec.log
fi
