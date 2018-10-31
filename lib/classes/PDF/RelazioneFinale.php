<?php

class PDF_RelazioneFinale extends FPDF
{
	protected $project;

	public function setProject(Project $project)
	{
		$this->project = $project;

		return $this;
	}

	public function getProject()
	{
		if ($this->project === null) {
			throw new PDF_Exception('Nessun Progetto impostato');
		}

		return $this->project;
	}

	public function drawLine($height)
	{
		$this->Line(
			$this->getPercX(05),
			$height,
			$this->getPercX(95),
			$height
		);

		return $this;
	}

	public function Footer()
	{
		$project = $this->getProject();

		$height = $this->getPercY(94);
		$this->drawLine($height);

		$footerLeft = 'Relazione Istruttoria   -   Progetto: '.$project->getId().'   -   '.date('d/m/Y');
		$footerRight = 'Pagina '.$this->PageNo().' di {nb}';

		$this->SetFont('Arial', '', 10);
		$height += 3;
		$this->SetY($height);

		$this->SetX($this->getPercX(05));
		$this->Cell(0, 0, $footerLeft);

		$this->SetX($this->getPercX(50));
		$this->Cell($this->getPercX(47), 0, $footerRight, 0, 0, 'R');

		return $this;
	}
}
