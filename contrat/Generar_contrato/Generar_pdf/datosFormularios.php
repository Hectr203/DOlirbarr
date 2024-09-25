<?php
require "../Generar_pdf/generar_contrato.php";

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

$langs->loadLangs(array("contracts", "orders", "companies", "bills", "products", 'compta'));


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


            
            // Establece la zona horaria a la de México
            date_default_timezone_set('America/Mexico_City');
            
            // Formato "día de mes de año" usando date()
            $fecha = date('d') . ' de ' . date('F') . ' de ' . date('Y');
            
            // Reemplazar nombres de meses en inglés por los de español
            $meses = array(
                'January' => 'Enero',
                'February' => 'Febrero',
                'March' => 'Marzo',
                'April' => 'Abril',
                'May' => 'Mayo',
                'June' => 'Junio',
                'July' => 'Julio',
                'August' => 'Agosto',
                'September' => 'Septiembre',
                'October' => 'Octubre',
                'November' => 'Noviembre',
                'December' => 'Diciembre'
            );
            
            // Reemplazar el mes en inglés por el mes en español
            $fecha = str_replace(array_keys($meses), array_values($meses), $fecha);
            
            
            
            

            // Cargar la plantilla HTML
            $html = file_get_contents('../Plantillas/plantilla_contrato.html');

            // Reemplazar las variables en la plantilla con los datos reales
            $html = str_replace('{{folio}}', $folio, $html);
            $html = str_replace('{{FECHA}}', $fecha, $html);

            $html = str_replace('{{cliente_nom}}', htmlspecialchars($cliente->nom), $html);
            $html = str_replace('{{cliente_name_alias}}', htmlspecialchars($cliente->name_alias), $html);

            $html = str_replace('{{cliente_address}}', htmlspecialchars($cliente->address), $html);
            $html = str_replace('{{cliente_zip}}', htmlspecialchars($cliente->zip), $html);
            $html = str_replace('{{cliente_town}}', htmlspecialchars($cliente->town), $html);
            $html = str_replace('{{cliente_phone}}', htmlspecialchars($cliente->phone), $html);
            $html = str_replace('{{cliente_email}}', htmlspecialchars($cliente->email), $html);

            // Llamar a la función que genera el PDF
            generar_pdf_contrato($html,$folio);
        } else {
            setEventMessages('Cliente no encontrado.', null, 'mesgs');

           
        }
    } else {
        setEventMessages('Error en la consulta SQL.', null, 'mesgs');

    }
}
