<?php
require('fpdf.php');

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "skyper";
$password = "ctpalm2113";
$bd = "estadiaunid";

$id_registro = isset($_GET['id_registro']) ? intval($_GET['id_registro']) : 0; // Recibe el ID por GET
$GLOBALS['depa']; // Define tu departamento
$GLOBALS['jfedpa'];// Define el jefe del departamento
$GLOBALS['super'];// Define el superintendente gral
$GLOBALS['superman'];// Define el superintendente de mantenimiento
try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$bd;charset=utf8", $usuario, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta principal para obtener el registro específico
    $stmt = $pdo->prepare("SELECT * FROM registros WHERE id = ?");
    $stmt->execute([$id_registro]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($registros)) {
        die("No se encontraron registros con el ID proporcionado.");
    }
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}

class PDF extends FPDF {
    function Header() {
        global $depa;
        $this->Image('../imagenes/svg/cfe_icon.png', 10, 8, 50);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(300, 5, 'CENTRAL TERMOELECTRICA PRESIDENTE ADOLFO LOPEZ MATEOS', 0, 1, 'C');
        $this->Cell(300, 5, 'SOLICITUD DE AUTORIZACION DE TIEMPO EXTRAORDINARIO', 0, 1, 'C');
        $this->Cell(300, 5, 'DEPARTAMENTO' . $depa, 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-30);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(90, 5, 'Vo.Bo.', 0, 0, 'C');
        $this->Cell(90, 5, 'Vo.Bo.', 0, 0, 'C');
        $this->Cell(90, 5, 'AUTORIZA', 0, 1, 'C');
        $this->Cell(90, 0, '', 'T', 0, 'C');
        $this->Cell(90, 0, '', 'T', 0, 'C');
        $this->Cell(90, 0, '', 'T', 1, 'C');
        $this->Ln(2);
        $this->Cell(90, 5, 'ING.'. $jfedpa , 0, 0, 'C');
        $this->Cell(90, 5, 'ING. CESAR IVAN CRUZ CHAVEZ', 0, 0, 'C');
        $this->Cell(90, 5, 'ING. APOLINAR ORTIZ VALENCIA', 0, 1, 'C');
        $this->Cell(90, 5, 'JEFE DE DEPARTAMENTO ELÉCTRICO', 0, 0, 'C');
        $this->Cell(90, 5, 'SUPERINTENDENTE DE MANTENIMIENTO', 0, 0, 'C');
        $this->Cell(90, 5, 'SUPERINTENDENTE GENERAL', 0, 0, 'C');
    }

    // (Tu método ReportBody aquí)
}

$pdf = new PDF('L');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->ReportBody($registros[0], $pdo); // Asegúrate de tener registros válidos
$pdf->Output();
?>
