<?php
require_once __DIR__ . '/vendor/autoload.php';
ini_set('set_time_limit', 60);
ini_set('auto_detect_line_endings', TRUE);
$row = 1;

$folder_path = __DIR__ . '/pdfs';
mkdir($folder_path);
$folder_path = realpath($folder_path);

if (($handle = fopen($_FILES['csv_file']['tmp_name'], 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
        if ($row == 1) {
            $row++;
            continue;
        } else {
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8','format' => 'A4','margin_left' => 10,'margin_right' => 10,'margin_top' => 10,'margin_bottom' => 10,'margin_header' => 10,'margin_footer' => 10]);
            $mpdf->useSubstitutions = false;
            $html = '
            <div style="width: 100%">
                <h3 style="text-transform: uppercase; text-align: center; margin-bottom: 20px; background-color: #f3f3ff">Formulário para aplicação de preenchedores</h3>
            </div>
            <div style="width: 100%">
                <div style="width: 25%; float: left">';
                if (isset($_FILES['pdf_image'])) {
                    move_uploaded_file($_FILES['pdf_image']['tmp_name'], $folder_path . $_FILES['pdf_image']['name']);
                    $html .= '<img src="'.$folder_path . $_FILES['pdf_image']['name'].'" />';
                } else {
                    $html .= '<img src="face_picture.jpeg" />';
                }
                $html .= '
                </div>
                <div style="width: 23%; float: left; padding-left: 11px; font-size: 12px">
                    <p><strong>Paciente:</strong> '.utf8_encode($data[2]).'</p>
                    <p><strong>Data de nascimento:</strong> '.utf8_encode($data[8]).'</p>
                    <p><strong>Endereço:</strong> '.utf8_encode($data[3]).'</p>
                    <p><strong>Telefone:</strong> '.utf8_encode($data[0]).'</p>
                    <p><strong>Cidade:</strong> '.utf8_encode($data[6]).'</p>
                    <p><strong>CEP:</strong> '.utf8_encode($data[5]).'</p>
                    <p><strong>Bairro:</strong> '.utf8_encode($data[4]).'</p>
                </div>
                <div style="width: 23%; float: left; padding-left: 11px; font-size: 12px">
                    <p><strong>Documento:</strong> '.utf8_encode($data[1]).'</p>
                    <p><strong>Dermatite de contato:</strong> '.utf8_encode($data[9]).'</p>
                    <p><strong>Termo de consentimento assinado:</strong> '.utf8_encode($data[10]).'</p>
                    <p><strong>Aceita exposição do caso clínico:</strong> '.utf8_encode($data[11]).'</p>
                    <p><strong>Produto aplicado:</strong> '.utf8_encode($data[12]).'</p>
                    <p><strong>Marca utilizada:</strong> '.utf8_encode($data[13]).'</p>
                    <p><strong>Região aplicada:</strong> '.utf8_encode($data[14]).'</p>
                </div>
                <div style="width: 23%; float: left; padding-left: 11px; font-size: 12px">
                    <p><strong>Unidades no total:</strong> '.utf8_encode($data[15]).'</p>
                    <p><strong>Data da aplicação:</strong> '.utf8_encode($data[16]).'</p>
                    <p><strong>Diluição:</strong> '.utf8_encode($data[17]).'</p>
                    <p><strong>Lote:</strong> '.utf8_encode($data[18]).'</p>
                    <p><strong>Vencimento:</strong> '.utf8_encode($data[19]).'</p>
                    <p><strong>Pós clínico:</strong> '.utf8_encode($data[21]).'</p>
                    <p><strong>Efeito:</strong> '.utf8_encode($data[20]).'</p>
                </div>
            </div>';
            $mpdf->WriteHTML($html);
            $mpdf->Output($folder_path .'/'. str_replace(' ', '_', strtolower($data[2])).".pdf", \Mpdf\Output\Destination::FILE);
        }
    }
    fclose($handle);
}
// Nesse exemplo, será criado no mesmo diretório de onde está executando o script
$zip_file = basename($folder_path).'.zip';

// Inicializa o objeto ZipArchive
$zip = new ZipArchive();
$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Iterador de diretório recursivo
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($folder_path),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    // Pula os diretórios. O motivo é que serão inclusos automaticamente
    if (!$file->isDir()) {
        // Obtém o caminho normalizado da iteração corrente
        $file_path = $file->getRealPath();

        // Obtém o caminho relativo do mesmo.
        $relative_path = substr($file_path, strlen($folder_path) + 1);

        // Adiciona-o ao objeto para compressão
        $zip->addFile($file_path, $relative_path);
    }
}

// Fecha o objeto. Necessário para gerar o arquivo zip final.
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.basename($zip_file).'"');
header('Content-Length: ' . filesize($zip_file));

flush();
readfile($filename);

// delete files and folders
$files = glob($folder_path . '*', GLOB_MARK);
foreach ($files as $file) {
    if (is_dir($file)) {
        self::deleteDir($file);
    } else {
        unlink($file);
    }
}
rmdir($folder_path);
?>