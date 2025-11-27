<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/fpdf/fpdf.php";

if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') exit("Acceso denegado");

$pdo = Database::pdo();

// Conteos
$totalAlumnos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='alumno'")->fetchColumn();
$totalMaestros = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='maestro'")->fetchColumn();
$totalMaterias = $pdo->query("SELECT COUNT(*) FROM materias")->fetchColumn();

// Últimos 20 usuarios
$stmt = $pdo->query("SELECT username, nombre_completo, rol, created_at FROM usuarios ORDER BY id DESC LIMIT 20");
$usuarios = $stmt->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,utf8_decode('Reporte General del Sistema'),0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,10,utf8_decode('Generado por Superadmin - ' . date('d/m/Y H:i')),0,1,'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Sección Estadísticas
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,utf8_decode('Estadísticas'),0,1);
$pdf->SetFont('Arial','',12);

$pdf->Cell(60,10,'Total Alumnos: ' . $totalAlumnos, 1, 0);
$pdf->Cell(60,10,'Total Maestros: ' . $totalMaestros, 1, 0);
$pdf->Cell(60,10,'Total Materias: ' . $totalMaterias, 1, 1);
$pdf->Ln(10);

// Sección Usuarios Recientes
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,utf8_decode('Últimos Usuarios Registrados'),0,1);

$pdf->SetFillColor(230,230,230);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,10,'Usuario',1,0,'L',true);
$pdf->Cell(80,10,'Nombre',1,0,'L',true);
$pdf->Cell(30,10,'Rol',1,0,'C',true);
$pdf->Cell(30,10,'Fecha',1,1,'C',true);

$pdf->SetFont('Arial','',9);
foreach($usuarios as $u) {
    $pdf->Cell(50,8,utf8_decode($u['username']),1,0);
    $pdf->Cell(80,8,utf8_decode($u['nombre_completo']),1,0);
    $pdf->Cell(30,8,ucfirst($u['rol']),1,0,'C');
    $fecha = date('d/m/Y', strtotime($u['created_at']));
    $pdf->Cell(30,8,$fecha,1,1,'C');
}

$pdf->Output('I', 'Reporte_Sistema.pdf');
?>