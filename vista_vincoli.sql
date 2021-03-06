select sbapvr.vincoli_db.id AS vincolo_id,
	sbapvr.vincoli_db.comune AS comune,
	sbapvr.vincoli_db.localita AS localita,
	sbapvr.vincoli_db.provincia AS provincia,
	sbapvr.vincoli_db.fogliocatastale AS fogliocatastale,
	sbapvr.vincoli_db.particelle AS particelle,
	sbapvr.vincoli_db.modifichecatastali AS modifichecatastali,
	sbapvr.vincoli_db.denominazione AS denominazione,
	sbapvr.vincoli_db.ubicazioneinit AS ubicazioneinit,
	sbapvr.vincoli_db.ubicazioneprinc AS ubicazioneprinc,
	sbapvr.vincoli_db.vincolodiretto AS vincolodiretto,
	sbapvr.vincoli_db.vincoloindiretto AS vincoloindiretto,
	sbapvr.vincoli_db.dlgs422004 AS dlgs422004,
	sbapvr.vincoli_db.dl4901999 AS dl4901999,
	sbapvr.vincoli_db.l10891939 AS l10891939,
	sbapvr.vincoli_db.l3641909 AS l3641909,
	sbapvr.vincoli_db.provvedimentoministeriale AS provvedimentoministeriale,
	sbapvr.vincoli_db.trascrizioneinconservatoria AS trascrizioneinconservatoria,
	sbapvr.vincoli_db.note AS note,
	sbapvr.vincoli_db.posizionegeneralecomune AS posizionegeneralecomune,
	sbapvr.vincoli_db.cartellaprogettimonumentale AS cartellaprogettimonumentale,
	sbapvr.vincoli_db.eventualesubposizione AS eventualesubposizione,
	sbapvr.vincoli_db.fascicolovincolo AS fascicolovincolo,
	sbapvr.vincoli_db.fascicoloprogetti AS fascicoloprogetti,
	sbapvr.vincoli_db.tabella AS tabella,
	sbapvr.vincoli_db.visibile AS visibile,concat(sbapvr.vincoli_db.posizionegeneralecomune,'/',if((sbapvr.vincoli_db.cartellaprogettimonumentale = NULL or sbapvr.vincoli_db.cartellaprogettimonumentale = '' ),concat(sbapvr.vincoli_db.eventualesubposizione,'/',sbapvr.vincoli_db.fascicoloprogetti),sbapvr.vincoli_db.cartellaprogettimonumentale)) AS posizioneMonumentale,
concat(sbapvr.vincoli_db.posizionegeneralecomune,'/',if((sbapvr.vincoli_db.eventualesubposizione > ''),concat(sbapvr.vincoli_db.eventualesubposizione,'/',sbapvr.vincoli_db.fascicolovincolo),sbapvr.vincoli_db.fascicolovincolo)) AS posizioneVincoli 
from vincoli.vincoli_db
