<?php


class displayMail extends MimeMailParser {


    protected $_dataArrivo;

    protected $postaCert;

    public function getMessageBodyPostacert($type){
        if(is_a($this->postaCert, 'MimeMailParser')){
            return $this->postaCert->getMessageBody($type);
        } elseif($postacert = $this->getAttachedFile('postacert.eml')){
            $this->postaCert = new MimeMailParser();
            $this->postaCert->setText($postacert);
            return $this->postaCert->getMessageBody($type);
        }

        return parent::getMessageBody($type);
    }

	public function getMessageBodyRicevuta($type = 'text') {
		$body = false;
		$mime_types = array(
			'text'=> '/text\/plain/',
			'html'=> '/text\/html/'
			);
			if (in_array($type, array_keys($mime_types))) {
				foreach($this->parts as $part) {
					if ( preg_match($mime_types[$type],$this->getPartContentType($part)) ) {
						$headers = $this->getPartHeaders($part);
						$body .= $this->decode($this->getPartBody($part), array_key_exists('content-transfer-encoding', $headers) ? $headers['content-transfer-encoding'] : '');
					}
				}
			} else {
				throw new Exception('Invalid type specified for MimeMailParser::getMessageBody. "type" can either be text or html.');
			}
			return $body;
	}



	public function setText($data) {
		$this->resource = mailparse_msg_create();
		// does not parse incrementally, fast memory hog might explode
		mailparse_msg_parse($this->resource, $data);
		$this->data = $data;
		$this->parse();
		preg_match('/^[a-z]{3},(.*)/i',$this->getHeader('date'),$dateMatch);

		$this->_dataArrivo = date('d/m/Y',strtotime( trim($dateMatch[1]) ));

		return $this;
	}

	public function viewMail($pecId=null){
        $filesClass = array(
        	'bin' => 'fa-file-archive-o',
            'pdf' => 'fa-file-pdf-o',
            'txt' => 'fa-file-text-o',
            'xls' => 'fa-file-excel-0',
            'xml' => 'fa-file-code-o',
            'eml' => 'fa-envelope-o',
        	'p7m' => 'fa-file-zip-o',
        	'p7s' => 'fa-file-zip-o',
        	'png' => 'fa-file-image-o',
        	'jpg' => 'fa-file-image-o',
        	'jpeg' => 'fa-file-image-o',
        	'bmp' => 'fa-file-image-o',
        	'gif' => 'fa-file-image-o',

        );
		$to = $this->getHeader('to');
		$delivered_to = $this->getHeader('delivered-to');
		$from = $this->getHeader('from');
		$subject = $this->getHeader('subject');



        $subject = str_replace("_"," ", mb_decode_mimeheader($subject));

        if (!preg_match('/ACCETTAZIONE|CONSEGNA/', $subject)) {
            $text = $this->getMessageBodyPostacert('text');
            $html = $this->getMessageBodyPostacert('html');
        } else {
            $text = $this->getMessageBodyRicevuta('text');
            $html = $this->getMessageBodyRicevuta('html');
        }




		$txtBodyHeader = $this->getMessageBodyHeaders();

		if(preg_match('|UTF-8|i',$txtBodyHeader['content-type'])){
			$text = nl2br(htmlentities($text,ENT_COMPAT,'UTF-8'));
		} else {
		    $text = nl2br(htmlentities($text,ENT_COMPAT,'UTF-8'));
		    $html = utf8_encode($html);
		}



		$attachments = $this->getAttachments();


		print('<div onclick="printPec();" style="float:right; margin-right: 10px; cursor: pointer;" >
					<span style="font-weight: bold; color: #D56F2B; margin-right:5px;">Stampa PEC</span><img src="graphics/printer.png">
				</div>');
		print('<div id="pecToPrint">');
		// Assegnazione
		if($praticaId = Db_Pdo::getInstance()->query('SELECT pratica_id FROM arc_pratiche_pec WHERE pec_id = :pec_id',[
		    ':pec_id' => $pecId,
		])->fetchColumn()){
		    print('<div class="mailCert">');
		    $uo = Db_Pdo::getInstance()->query('SELECT
		            pratiche.numeroregistrazione,
		            date_format(pratiche.dataregistrazione,"%d/%m/%Y") as data,
		            arc_organizzazione.description
		            FROM pratiche
		            LEFT JOIN arc_pratiche_uo ON (arc_pratiche_uo.pratica_id = pratiche.pratica_id)
		            LEFT JOIN arc_organizzazione ON (arc_organizzazione.uoid = arc_pratiche_uo.uoid)
		            WHERE pratiche.pratica_id = :pratica_id',[
				                ':pratica_id' => $praticaId,
				            ])->fetchAll();
            print('<ul><h3>Prot nr. ' . $uo[0]['numeroregistrazione'] . ' del ' . $uo[0]['data'] . '</h3>');
            foreach ($uo as $assegnazione) {
                print('<li><b>' . $assegnazione['description'] . '</b></li>');
            }
            print('</ul>');
            print('</div>');
		}
        print('<div class="mailCert"><span>Data Arrivo:</span>' . $this->_dataArrivo . '</div>');
		print('<div class="mailCert"><span>Mittente:</span>' . $from . '</div>');
		print('<div class="mailCert"><span>Oggetto:</span>PEC_ID_' . $_GET['PEC_ID'] . ' ' . $subject . '</div>');
		if(!empty($html)){
			print('<div class="mailCert"><p>' . strip_tags($html,'<p><a><div><h1><h2><h3><h4><span><table><tr><td><br><font><b>') . '</p></div>');
		} else {
			print('<div class="mailCert"><p>' . $text . '</p></div>');
		}


		print('<div class="mailCert"><ul>Allegati:');
		$attachIndex = 0;
		foreach($attachments as $attachment) {
			$fileName = $attachment->filename;
			if(!empty($fileName)){
			    $fileName = str_replace("_"," ", mb_decode_mimeheader($fileName));
			    print('<li  onclick="pecDownload(this,'.$_GET['PEC_ID'].','.$attachIndex.')" style="cursor: pointer">
					<i class="fa '. $filesClass[strtolower($attachment->extension)] .'"> </i>  ' .  $fileName . '</li>');
			}
			$attachIndex++;
		}
		print('</ul></div>');
		print('</div>');


		return $this;
	}

	public function getP7mContent($content){
        try {
            //create tmpFilename
            $tmpName = TMP_PATH . DIRECTORY_SEPARATOR .'firma_' . session_id() . '.p7m';
            $attachment = new SplFileObject($tmpName, 'w+');
            $attachment->fwrite($content);

            $p7m = new P7M_Reader($attachment);
            $fileEstratto = $p7m->getOriginalFile();
            $extractedContent = '';
            if(method_exists($fileEstratto, 'fread')){
                $extractedContent = $fileEstratto->fread($fileEstratto->getSize());
            } else {
                while (! $fileEstratto->eof()) {
                    $extractedContent .= $fileEstratto->fgets();
                }
            }

            $attachment = null;
            foreach (glob(TMP_PATH . DIRECTORY_SEPARATOR .'firma_' . session_id() .'.*') as $tmpFile) {
                unlink($tmpFile);
            }

        } catch (Exception $e) {
            return false;
        }

	    return $extractedContent;
	}

    public function getAttachedFile($nomeFile){
        foreach ($this->getAttachments() as $attachment) {
            if(preg_match('/'. $nomeFile . '/i', $attachment->filename)){
        	    return $attachment->content;
        	}
        }

        return false;
    }
	public function getDataArrivo() {

		return $this->_dataArrivo;
	}

}

