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


// Asegurarse de que el usuario tiene derechos
if (!$user->rights->societe->creer) {
    accessforbidden();
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['token'] === $_SESSION['newtoken']) {
        $folio_contrato = $_POST['folio_contrato'];
        $userId = $_POST['userId'];

        procesar_folio_contrato($folio_contrato, $userId);
    } else {
        echo "Token de sesión no válido.";
    }
}

// Función para procesar el folio del contrato y generar el PDF
function procesar_folio_contrato($folio, $userId) {
    global $db;

    // Consulta para obtener los datos del cliente
    $sql = "SELECT nom,name_alias, address, zip, town, phone, email 
            FROM ".MAIN_DB_PREFIX."societe 
            WHERE rowid = ".((int) $userId);
            

    $resql = $db->query($sql);
    if ($resql) {
        $cliente = $db->fetch_object($resql);

        if ($cliente) {

              // Obtener la fecha actual en formato largo
                    // Establece la zona horaria a la de México
        date_default_timezone_set('America/Mexico_City');

        // Obtiene la fecha en formato dd/mm/yyyy
        $fechaMexico = date('d/m/Y');

        // Muestra la fecha


              
                setlocale(LC_TIME, 'es_MX.UTF-8');
                $FECHA = strftime('%d de %B de %Y');
            // Cargar la plantilla HTML
            $html = file_get_contents('../Plantillas/plantilla_contrato.html');

            // Reemplazar las variables en la plantilla con los datos reales
            $html = str_replace('{{folio}}', $folio, $html);
            $html = str_replace('{{FECHA}}', $fechaMexico, $html);

            $html = str_replace('{{cliente_nom}}', htmlspecialchars($cliente->nom), $html);
            $html = str_replace('{{cliente_name_alias}}', htmlspecialchars($cliente->name_alias), $html);

            $html = str_replace('{{cliente_address}}', htmlspecialchars($cliente->address), $html);
            $html = str_replace('{{cliente_zip}}', htmlspecialchars($cliente->zip), $html);
            $html = str_replace('{{cliente_town}}', htmlspecialchars($cliente->town), $html);
            $html = str_replace('{{cliente_phone}}', htmlspecialchars($cliente->phone), $html);
            $html = str_replace('{{cliente_email}}', htmlspecialchars($cliente->email), $html);

            // Llamar a la función que genera el PDF
            generar_pdf_contrato($html);
        } else {
            echo "Cliente no encontrado.";
        }
    } else {
        echo "Error en la consulta SQL.";
    }
}

// Función para generar el PDF utilizando Dompdf
function generar_pdf_contrato($html) {
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // Opcional: configurar el tamaño del papel y la orientación
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar el PDF
    $dompdf->render();

    // Enviar el PDF al navegador
    $dompdf->stream("contrato.pdf", array("Attachment" => false));
}