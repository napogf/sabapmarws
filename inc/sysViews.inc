<?
$skambienteAnalisiConsumi="create or replace view ANALISICONSUMI AS " .
			" select ae.emissione_id, " .
					"are.PUNTO_ID, " .
					"ap.PARAMETRO_ID, " .
					"ai.ASPETTO, " .
					"ae.date as DATE, " .
					"ai.UMIS, " .
					"(select sum(case " .
									"when (date_format(ae.date,'%m')>='04' " .
										"and (ss.date between str_to_date(concat('01-04-',year(ae.date)-1),'%d-%m-%Y') " .
										"and str_to_date(concat('31-12-',year(ae.date)),'%d-%m-%Y')) and sare.total='Y') " .
										"then valore_1 " .
									"when (date_format(ae.date,'%m')<'04' " .
										"and (ss.date between str_to_date(concat('01-04-',year(ae.date)-2),'%d-%m-%Y') " .
										"and str_to_date(concat('31-12-',year(ae.date)-1),'%d-%m-%Y')) " .
										"and sare.total='Y') then valore_1 " .
									"else 0 end ) from arc_dati ss " .
													"left join arc_piano_sorveglianza sai on (sai.piano_id = ss.piano_id) " .
													"left join arc_punti_campionamento sare on (sare.punto_id = sai.punto_id) " .
												"where ss.aspetto = ae.aspetto and sai.parametro_id = ai.parametro_id group by sai.parametro_id ) as LASTYEAR, " .
					"(select sum(case when (date_format(ae.date,'%m')> '03' " .
											"and (ss.date between str_to_date(concat('01-04-',year(ae.date)),'%d-%m-%Y') " .
											"and str_to_date(concat('31-12-',year(ae.date)+1),'%d-%m-%Y')) " .
											"and sare.total='Y') then valore_1 " .
									"when (date_format(ae.date,'%m')<'04' " .
											"and (ss.date between str_to_date(concat('01-04-',year(ae.date)-1),'%d-%m-%Y') " .
											"and str_to_date(concat('31-12-',year(ae.date)),'%d-%m-%Y')) " .
											"and sare.total='Y') then valore_1 " .
									"else 0 end ) from arc_dati ss " .
													"left join arc_piano_sorveglianza sai on (sai.piano_id = ss.piano_id) " .
													"left join arc_punti_campionamento sare on (sare.punto_id = sai.punto_id) " .
													"where ss.aspetto = ae.aspetto and sai.parametro_id = ai.parametro_id group by sai.parametro_id ) as TOTYEAR , " .
					"are.description as PIANO_ID , " .
					"ap.description as PARAMETRO , " .
					"case when (date_format(ae.date,'%m')> '03' " .
								"and (ae.date between str_to_date(concat('01-04-',year(ae.date)),'%d-%m-%Y') " .
								"and str_to_date(concat('31-12-',year(ae.date)+1),'%d-%m-%Y'))) " .
							"then date_format(str_to_date(year(ae.date),\"%Y\"),\"%Y\") " .
						"when (date_format(ae.date,'%m')<'04' " .
								"and (ae.date between str_to_date(concat('01-04-',year(ae.date)-1),'%d-%m-%Y') " .
								"and str_to_date(concat('31-12-',year(ae.date)),'%d-%m-%Y'))) " .
							"then date_format(str_to_date(year(ae.date)-1,\"%Y\"),\"%Y\") end as YEAR ," .
					"apro.ore as OREANNO , " .
					"apro.macchine as PEZZIANNO , " .
					"round(ae.valore_1,2) as \"QUANTITY\" " .
				"from arc_dati ae " .
				"left join arc_piano_sorveglianza ai on (ai.piano_id = ae.piano_id) " .
				"left join arc_punti_campionamento are on (are.punto_id = ai.punto_id) " .
				"left join arc_parametri ap on (ap.parametro_id = ai.parametro_id) " .
				"left join arc_produzione apro on (apro.anno = (case when (date_format(ae.date,'%m')> '03' " .
																	"and (ae.date between str_to_date(concat('01-04-',year(ae.date)),'%d-%m-%Y') " .
																	"and str_to_date(concat('31-12-',year(ae.date)+1),'%d-%m-%Y'))) " .
																"then date_format(str_to_date(year(ae.date),\"%Y\"),\"%Y\") " .
																"when (date_format(ae.date,'%m')<'04' " .
																	"and (ae.date between str_to_date(concat('01-04-',year(ae.date)-1),'%d-%m-%Y') " .
																	"and str_to_date(concat('31-12-',year(ae.date)),'%d-%m-%Y'))) " .
																"then date_format(str_to_date(year(ae.date)-1,\"%Y\"),\"%Y\") end) ) " .
				"where ae.ASPETTO IN ('01', '04','05','06') " .
				"order by DATE , PIANO_ID , PARAMETRO ;";





print($skambienteAnalisiConsumi."<br>\n");

$flussoEmissioni="create or replace view FLUSSOEMISSIONI AS " .
		"select ad.* from arc_dati ad " .
		"right join arc_piano_sorveglianza ap on (ad.piano_id = ap.piano_id) " .
		"right join arc_punti_campionamento ac on (ap.punto_id = ac.punto_id) " .
		" where ad.aspetto='07' and ac.total='F' ;";
print($flussoEmissioni."<br>\n");

$concEmissioni="create or replace view CONCEMISSIONI AS " .
		"select ad.* from arc_dati ad " .
		"right join arc_piano_sorveglianza ap on (ad.piano_id = ap.piano_id) " .
		"right join arc_punti_campionamento ac on (ap.punto_id = ac.punto_id) " .
		" where ad.aspetto='07' and ac.total<>'F' ;";
print($concEmissioni."<br>\n");


$datiEmissioni="CREATE OR REPLACE VIEW v_dati_emissioni AS
						SELECT ae.ASPETTO, " .
						"ae.DATE, " .
						"ae.EMISSIONE_ID, " .
						"ae.PIANO_ID, " .
						"ae.VALORE_1, " .
						"ae.WARNING, " .
						"(						
						CASE WHEN are.total = 'F'
						THEN round( ae.valore_2 *1000000 / ae.valore_1, 4 )
						ELSE valore_2
						END
						) AS VALORE_2, (
						
						CASE WHEN are.total = 'F'
						THEN valore_2
						ELSE round( ae.valore_1 * ae.valore_2 /1000000, 4 )
						END
						) AS FLUSSO, " .
						"ai.limite_legge AS LIMITE_LEGGE, " .
						"ai.limite_interno AS LIMITE_INTERNO, " .
						"flusso_ll AS FMLL, flusso_li AS FMLI, " .
						"ai.portata AS PORTATA
						FROM arc_dati ae
						LEFT JOIN arc_piano_sorveglianza ai ON ( ai.piano_id = ae.piano_id )
						LEFT JOIN arc_parametri app ON ( app.parametro_id = ai.parametro_id )
						LEFT JOIN arc_punti_campionamento are ON ( (
						are.punto_id = ai.punto_id
						)
						AND (
						are.aspetto = '07'
						) )
						WHERE ae.aspetto = '07';";
print($datiEmissioni."<br>\n");


$systemViews="" .
"create or replace view dir_labels as select * from sksystem.dir_labels ;
create or replace view dir_links as select * from sksystem.dir_links ;
create or replace view dir_resp_reference as select * from sksystem.dir_resp_reference ;
create or replace view directories as select * from sksystem.directories ;
create or replace view languages as select * from sksystem.languages ;
create or replace view link_audit as select * from sksystem.link_audit ;
create or replace view log_audit as select * from sksystem.log_audit ;
create or replace view program_language_buttons as select * from sksystem.program_language_buttons ;
create or replace view program_language_labels as select * from sksystem.program_language_labels ;
create or replace view r_anagrafiche as select * from sksystem.r_anagrafiche ;
create or replace view r_autorizzazzioni as select * from sksystem.r_autorizzazzioni ;
create or replace view r_classi as select * from sksystem.r_classi ;
create or replace view r_mezzi as select * from sksystem.r_mezzi ;
create or replace view r_movimenti as select * from sksystem.r_movimenti ;
create or replace view r_rifiuti as select * from sksystem.r_rifiuti ;
create or replace view rc_mezzi_classi_rifiuti as select * from sksystem.rc_mezzi_classi_rifiuti ;
create or replace view rc_mezzi_trasportatori as select * from sksystem.rc_mezzi_trasportatori ;
create or replace view resp_lang_descriptions as select * from sksystem.resp_lang_descriptions ;
create or replace view responsabilities as select * from sksystem.responsabilities ;
create or replace view sys_buttons as select * from sksystem.sys_buttons ;
create or replace view sys_counters as select * from sksystem.sys_counters ;
create or replace view sys_fields_validations as select * from sksystem.sys_fields_validations ;
create or replace view sys_form_button_labels as select * from sksystem.sys_form_button_labels ;
create or replace view sys_form_buttons as select * from sksystem.sys_form_buttons ;
create or replace view sys_forms as select * from sksystem.sys_forms ;
create or replace view sys_forms_buttons as select * from sksystem.sys_forms_buttons ;
create or replace view sys_forms_fields as select * from sksystem.sys_forms_fields ;
create or replace view sys_forms_fields_labels as select * from sksystem.sys_forms_fields_labels ;
create or replace view sys_forms_titles as select * from sksystem.sys_forms_titles ;
create or replace view sys_menu as select * from sksystem.sys_menu ;
create or replace view sys_menu_labels as select * from sksystem.sys_menu_labels ;
create or replace view sys_menu_names as select * from sksystem.sys_menu_names ;
create or replace view sys_menu_resp_ref as select * from sksystem.sys_menu_resp_ref ;
create or replace view sys_messages as select * from sksystem.sys_messages ;
create or replace view sys_pages_functions as select * from sksystem.sys_pages_functions ;
create or replace view sys_pages_functions_labels as select * from sksystem.sys_pages_functions_labels ;
create or replace view sys_pagfun_resp_ref as select * from sksystem.sys_pagfun_resp_ref ;
create or replace view sys_user_resp_reference as select * from sksystem.sys_user_resp_reference ;
create or replace view syd_users as select * from sksystem.sys_users ;" .
"create or replace view user_resp_reference as select * from sksystem.user_resp_reference ;
create or replace view users as select * from sksystem.users ;
create or replace view wrk_activities as select * from sksystem.wrk_activities ;
create or replace view wrk_data as select * from sksystem.wrk_data ;
create or replace view wrk_data_files as select * from sksystem.wrk_data_files ;
create or replace view wrk_status as select * from sksystem.wrk_status ;
create or replace view wrk_steps as select * from sksystem.wrk_steps ;
create or replace view wrk_steps_lang as select * from sksystem.wrk_steps_lang ;
create or replace view wrk_steps_resp_ref as select * from sksystem.wrk_steps_resp_ref ;
create or replace view wrk_works as select * from sksystem.wrk_works ;
create or replace view wrk_works_types as select * from sksystem.wrk_works_types ;
create or replace view wrk_works_types_lang as select * from sksystem.wrk_works_types_lang ;
";

print($systemViews."<br>\n");
?>