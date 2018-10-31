<?php
function parse_wiki($template){
	print('<div id="wiki">');
		if (file_exists($template)) {
			$fcontents = file ($template);
			while ((list ($line_num, $line) = each ($fcontents))) {
				$breakLine=TRUE;
				$line=strip_tags($line);
				// Links a Schede
				$regexp="|{(link_scheda).*}|U";
				// $regexp="(*)";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						switch($test[1][$i]){
							case 'link_scheda':
									preg_match_all("[#id_(.+)#]",$test[0][$i],$id);
									preg_match_all("[#desc_(.+)}]",$test[0][$i],$desc);
									$fld_replace='<a href="#" onclick="javascript:popscheda('.$id[1][0].')" >'.$desc[1][0].'</a>';
								break;

							default:
								$fld_replace=substr($test[1][0],13,99);
						} // switch
				    	$line=preg_replace('['.$test[0][$i].']', $fld_replace, $line);
					} // for
				}

				// Links esterni
				$regexp="|{(link_www).*}|U";
				// $regexp="(*)";
				$founded=preg_match_all($regexp, "$line", $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						switch($test[1][$i]){
							case 'link_www':

									preg_match_all("[#www_(.+)#]",$test[0][$i],$id);
									preg_match_all("[#desc_(.+)}]",$test[0][$i],$desc);
									$fld_replace='<a href="'.$id[1][0].'" target="_blank" >'.$desc[1][0].'</a>';
								break;

							default:
								$fld_replace=substr($test[1][0],13,99);
						} // switch
						$regExpTest=str_replace('?','\?',$test[0][$i]);
				    	$line=preg_replace('['.$regExpTest.']', $fld_replace, $line);

				    	$line=preg_replace($test[0][$i], $fld_replace, $line);
					} // for
				}


				// Center
				$regexp="|{centrato(.*)}|U";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						$fld_replace='<div align="center">'.$test[1][$i].'</div>';
				    	$line=preg_replace('['.$test[0][$i].']', $fld_replace, $line);
					} // for
				}
				// Bold
				$regexp="|{grass(.*)}|U";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						$fld_replace='<B>'.$test[1][$i].'</B>';
				    	$line=preg_replace('['.$test[0][$i].']', $fld_replace, $line);
					} // for
				}
				// Bold
				$regexp="|{cors(.*)}|U";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						$fld_replace='<I>'.$test[1][$i].'</I>';
				    	$line=preg_replace('['.$test[0][$i].']', $fld_replace, $line);
					} // for
				}

				// Headers
				$regexp="|{H(.)(.*)}|U";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						$fld_replace='<H'.$test[1][$i].'>'.$test[2][$i].'</H1>';
				    	$line=preg_replace('['.$test[0][$i].']', $fld_replace, $line);
					} // for
				}

				// Images
				$regexp="[{img_#lar_(.*)#pos_(.*)#did_(.*)#(.*)}]U";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						$width=$test[1][$i]>0?$test[1][$i]:80;
						switch($test[2][$i]){
							case 'destra':
								$align='right';		;
								break;
							case 'sinistra':
								$align='left';		;
								break;
							default:
								$align='none';		;
						} // switch
						$didascalia=$test[3][$i];
						$src =  '<div class="figure" align="center" style="float:'.$align.'; width:'.(strval($width)+20).'px;" >';
						$src .= '<img src="'.$test[4][$i].'" width="'.$width.'">';
						$src .= '<p>'.$didascalia.'</p></div>';
					    $line=preg_replace('['.$test[0][$i].']', $src, $line);
					}
				}

				// Index
				$regexp="[{indice_(.*)#(.*)}]U";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						$index='#'.$test[1][$i];
						$href='<li><a href="'.$index.'">'.$test[2][$i].'</a></li>';
					    $line=preg_replace('['.$test[0][$i].']', $href, $line);
					    $breakLine=FALSE;
					}
				}

				$regexp="[{cap_(.*)#(.*)}]U";
				$founded=preg_match_all($regexp, $line, $test);
				if ($founded>0) {
					for($i = 0; $i < $founded; $i++){
						$href='<a name="'.$test[1][$i].'" style="font-weight: normal;" >'.$test[2][$i].'</a>';
					    $line=preg_replace('['.$test[0][$i].']', $href, $line);
					}
				}

				$newLine=$breakLine?'<br>':'';
				$line=preg_replace("[\n]", $newLine, $line);

				print($line);
			}
		} else {
			errore("Template $template does not exist! call system administrator");
		}
//	print('<div dojoType="dialog" id="dialog1" ' .
//			'bgColor="red" ' .
//			'bgOpacity="0.1" ' .
//			'toggle="fade" ' .
//			'toggleDuration="250" lifetime="5000">
//	Disappearing in <span id="timer1">3</span>... <a id="hider1" href="#">[X]</a>' .
//			'</div>');
	print('</div>');
}
?>