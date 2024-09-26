<?php
require_once ("../../../core/lib/dompdf/autoload.inc.php");
use Dompdf\Dompdf;



require "../../../main.inc.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/contract.lib.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/contract/modules_contract.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
// Asegúrate de que el token de sesión sea válido aquí, si es necesario

// Define la función para procesar el folio del contrato



// Función para generar el PDF utilizando Dompdf
function generar_pdf_contrato($html,$folio) { 
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // Opcional: configurar el tamaño del papel y la orientación
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar el PDF
    $dompdf->render();

    // Enviar el PDF al navegadorryzz
  echo  $dompdf->stream($folio.".pdf", array("Attachment" => false));
}