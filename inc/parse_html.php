<?php
function parse_html($template){
		if (file_exists($template)) {
			$fcontents = file ($template);
			$parse=false;
			while ((list ($line_num, $line) = each ($fcontents))) {
				if (preg_match('[<(BODY|body).*>]',$line,$body)>0) {
				    $parse=TRUE;
					$line=preg_replace('[<(BODY|body).*>]','',$line);
				}
				if ($parse) {
						// preg_replace('[<(FONT/font).*>]', '',$line);
						// preg_replace('[</(FONT|font)>]', '', $line);
						$find_font=preg_match_all('[<FONT|<font.*(face=".+").*>]U',$line, $font);
						if ($find_font>0) {
//							print_r($font);
//							for($x = 0; $x < $find_font; $x++){
//								$found_face=preg_match_all('[face="(.+)"]',$font[0][$x],$face);
//								if ($found_face>0) {
//								    // print_r($face);
//								}
//						    	$line=preg_replace('['.$font[0][$x].']', '', $line);
//							} // for
//
							$line=preg_replace('[face="(.+)"]U', 'face="Verdana, Arial, Helvetica, sans-serif"', $line);
							// $line=preg_replace('[face="(.+)"]U', '', $line);

						}
						$regexp="|{(link_scheda).*}|U";
						// $regexp="(�*�)";
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
						$regexp="[{(img.+)}]";
						preg_match_all($regexp, $line, $test);
						if ($test[1][0]>null) {

							if ($result['ROWS'][0]['UPLOADS_TYPE']=='S') {							//$src='get_file.php?f='.str_replace("+","�",$result['rows'][0]['FILENAME']).'&wk_link_id='.$result['rows'][0]['LINK_ID'].'&wk_inline=Y';
								$src='thumbnail.php?gd=2&src='.str_replace("+","�",$dir_upload.$result['rows'][0]['FILENAME']).'&maxw=250';
							} elseif ($result['ROWS'][0]['UPLOADS_TYPE']=='F'){
								$src=get_mime($result['ROWS'][0]['FILENAME']).' width="30" ';
							}

						    $line=preg_replace($regexp, $src, $line);
						}
						if (preg_match("[<(/BODY|/body)>]",$line,$body)>0) {
							$line=preg_replace('['.$body[0].']','',$line);
						    $parse=FALSE;
						}
						print($line);
				}
			}
		} else {
			errore("Template $template does not exist! call system administrator");
		}
}
?>