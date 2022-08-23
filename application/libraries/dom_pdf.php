<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'third_party/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;
class Dom_pdf extends Dompdf{
		
	public function __construct() {
    	parent::__construct(); 
		$options = new Options();
		// $options->set('defaultFont', 'Courier');
		$options->set('isRemoteEnabled', TRUE);
		$options->set('debugKeepTemp', TRUE);
		$options->set('isHtml5ParserEnabled', true);
    $pdf = new Dompdf($options);
		$contxt = stream_context_create([ 
		    'ssl' => [ 
		        'verify_peer' => FALSE, 
		        'verify_peer_name' => FALSE,
		        'allow_self_signed'=> TRUE
		    ] 
		]);
		$pdf->setHttpContext($contxt);
		$CI =& get_instance();
		$CI->dompdf = $pdf;
		
	}
	
}