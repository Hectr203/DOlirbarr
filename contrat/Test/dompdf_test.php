<?php
// Incluir el archivo principal de Dolibarr

// Incluir DOMPDF (asegúrate de que esta ruta es correcta)
require_once ("../../core/lib/dompdf/autoload.inc.php");
use Dompdf\Dompdf;

// Crear instancia de DOMPDF
$dompdf = new Dompdf();

// Definir el contenido HTML
$html = '
    <html>
    <head><title>Test DOMPDF</title></head>
    <body>
        <h1>Hola, este es un archivo de prueba con DOMPDF.</h1>
        <p>Generado utilizando la biblioteca DOMPDF.</p>
    </body>
    </html>
';

// Cargar el contenido HTML
$dompdf->loadHtml($html);

// Opcional: Configurar el tamaño y orientación de la página
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Mostrar el PDF en el navegador
$dompdf->stream("prueba_dompdf.pdf", ["Attachment" => false]);
