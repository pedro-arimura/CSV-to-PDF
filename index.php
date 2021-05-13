<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transformar CSV em PDF</title>
</head>
<body>
    <form action="generate_pdf.php" method="POST" enctype="multipart/form-data">
        <h2>Insira um arquivo CSV</h2>
        <input type="file" name="csv_file" id="csv_file" />
        <h2>Insira uma imagem (opcional)</h2>
        <input type="file" name="pdf_image" id="pdf_image" />
        <input type="submit" value="Gerar PDFs" style="display: block; margin-top: 30px"/>
    </form>
</body>
</html>