<?php

class PDF_BancaNuova extends FPDF
{
	public function Header()
	{
		$width = $this->getPercX(35);

		$this->Image(
			dirname(__FILE__).'/logo-banca-nuova.gif',
			($this->getPercX(50) - $width / 2 - $this->getPercX(3)), 10,
			$width
		);
	}

	public function Footer()
	{
		$height = $this->getPercY(90);

		$this->Line(
			$this->getPercX(05),
			$height,
			$this->getPercX(95),
			$height
		);

		$footer = array(
			'SOCIETÀ PER AZIONI - SEDE LEGALE E DIREZIONE GENERALE: I - 90141 PALERMO - VIA VAGLICA 22 - TEL. +39.091.380.5111 - FAX +39.091.322.906 – www.bancanuova.it',
			'CAPITALE SOCIALE € 29.783.868,00 - ADERENTE AL FONDO INTERBANCARIO DI TUTELA DEI DEPOSITI',
			'ISCRITTA AL N. 2009.9.0 DELL\'ALBO DELLE BANCHE E DEI GRUPPI BANCARI - ISCRITTA AL REA DI PALERMO N. 135604',
			'NUMERO DI ISCRIZIONE AL REGISTRO IMPRESE DI PALERMO, CODICE FISCALE E PARTITA IVA 00058890815 - CODICE ABI 05132',
			'APPARTENENTE AL GRUPPO BANCARIO "BANCA POPOLARE DI VICENZA"',
			'E SOGGETTA ALL’ATTIVITÀ DI DIREZIONE E COORDINAMENTO DELLA STESSA BANCA POPOLARE DI VICENZA',
		);

		$height += 2;
		$this->SetX(0);
		$this->SetFontSize(5);

		foreach ($footer as $line) {
			$this->SetY($height);
			$this->Cell(
				$this->getPercY(65),
				2,
				$line,
				0,
				0,
				'C'
			);

			$height += 2;
		}
	}
}
