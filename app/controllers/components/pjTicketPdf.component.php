<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjTicketPdf
{
	private $hash = '';
	
	private $barcode_value = '';
	
	public function __construct()
	{
		mt_srand();
		$this->hash = mt_rand(1000, 9999);
	}
	
	public function setBarcode($value)
	{
		$this->barcode_value = $value;
		return $this;
	}
	
	public function generateBarcode()
	{
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		
		require_once $dm->getPath('barcode') . 'autoload.php';
		
		$font = new BCGFontFile(PJ_INSTALL_PATH . 'app/web/obj/Arial.ttf', 12);

		$color_black = new BCGColor(0, 0, 0);
		$color_white = new BCGColor(255, 255, 255);

		$drawException = null;
		try {
			$code = new BCGcode39();
			$code->setScale(1);
			$code->setThickness(18);
			$code->setForegroundColor($color_black);
			$code->setBackgroundColor($color_white);
			$code->setFont($font);
			$code->setChecksum(false);
			$code->parse($this->barcode_value);
		} catch (Exception $e) {
			$drawException = $e;
		}
		$filename = PJ_UPLOAD_PATH.'tickets/barcodes/b_'. $this->barcode_value .'.png';
		$drawing = new BCGDrawing($filename, $color_white);
		if ($drawException) {
			$drawing->drawException($drawException);
		} else {
			$drawing->setBarcode($code);
			$drawing->draw();
		}
		$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
		return $filename;
	}
	
	public function generateTicket($ticket_img, $ticket_id)
	{
		$ticket = $ticket_img;
				
		if (is_file($ticket))
		{
			$ticketSize = getimagesize($ticket);
			switch ($ticketSize[2])
			{
				case IMAGETYPE_GIF:
					$dest = imagecreatefromgif($ticket);
					break;
				case IMAGETYPE_PNG:
					$dest = imagecreatefrompng($ticket);
					break;
				case IMAGETYPE_JPEG:
					$dest = imagecreatefromjpeg($ticket);
					break;
			}
		} else {
			$dest = imagecreate(510, 280);
			$background = imagecolorallocate($dest, 255, 255, 255);
		}
		
		$this->barcode_value = $ticket_id;
		
		$barcode = $this->generateBarcode();
		$barcodeSize = getimagesize($barcode);
		switch ($barcodeSize[2])
		{
			case IMAGETYPE_GIF:
				$src = imagecreatefromgif($barcode);
				break;
			case IMAGETYPE_PNG:
				$src = imagecreatefrompng($barcode);
				break;
			case IMAGETYPE_JPEG:
				$src = imagecreatefromjpeg($barcode);
				break;
		}
		$filename = PJ_UPLOAD_PATH . 'tickets/tickets/t_' . $this->barcode_value . '.png';
		imagecopymerge($dest, $src, 234, 219, 0, 0, $barcodeSize[0], $barcodeSize[1], 100);
		imagepng($dest, $filename, 9);
		imagedestroy($src);
		imagedestroy($dest);
		return $filename;
	}
	
	public function generatePdf($params)
	{
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		
		require_once($dm->getPath('tcpdf') . 'tcpdf.php');
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(10, 10, 10);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		$pdf->SetFont('dejavusans', '', 8);
		
		$uuid = '';
		
		foreach($params as $v)
		{
			$ticket = $this->generateTicket($v['ticket_img'], $v['ticket_id']);
			
			$pdf->AddPage();
			$pdf->Image($ticket, 10, 10, '', '', 'PNG', '', 'T', false, 300, '', false, false, 0, true, false, true);
			$pdf->Ln(100);
			
			$html = '<p style="color: #000; border:none;">' . $v['ticket_info'] . '</p>';
			$pdf->writeHTMLCell(87, 19, 13, 68, $html, 0);
			
			$uuid = $v['uuid'];
		}
		
		$pdf->Output(PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'tickets/pdfs/p_'. $uuid .'.pdf', 'F');
		$filename = PJ_UPLOAD_PATH . 'tickets/pdfs/p_'. $uuid . '.pdf';
		return $filename;
	}
}

?>