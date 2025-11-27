<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/fpdf/fpdf.php";

if (!Auth::isLoggedIn() || Auth::rol() !== 'alumno') exit("Acceso denegado");

$alumnoId = Auth::userId();
$nombreAlumno = Auth::nombreCompleto();
$pdo = Database::pdo();

// 1. Obtener materias y notas
// Usamos una consulta para traer materias y luego procesaremos las notas
$sql = "SELECT m.id, m.nombre, m.codigo, m.grupo, m.unidades 
        FROM inscripciones i
        JOIN materias m ON i.materia_id = m.id
        WHERE i.alumno_id = ? ORDER BY m.nombre";
$stmt = $pdo->prepare($sql);
$stmt->execute([$alumnoId]);
$materias = $stmt->fetchAll();

// Obtener todas las calificaciones del alumno
$stmt = $pdo->prepare("SELECT materia_id, calificacion FROM calificaciones WHERE alumno_id = ?");
$stmt->execute([$alumnoId]);
$notasRaw = $stmt->fetchAll();

// Organizar notas
$notas = [];
foreach($notasRaw as $row) {
    $notas[$row['materia_id']][] = $row['calificacion'];
}

// --- GENERACIÓN DEL PDF ---

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,utf8_decode('Reporte de Calificaciones (Kárdex)'),0,1,'C');
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
$pdf->SetFont('Arial','',12);

// Info del Alumno
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,utf8_decode('Alumno:'),0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,utf8_decode($nombreAlumno),0,1);
$pdf->Cell(40,10,utf8_decode('Fecha:'),0,0);
$pdf->Cell(0,10,date('d/m/Y'),0,1);
$pdf->Ln(10);

// Tabla de Materias
$pdf->SetFillColor(200,220,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,10,utf8_decode('Código'),1,0,'C',true);
$pdf->Cell(80,10,utf8_decode('Materia'),1,0,'C',true);
$pdf->Cell(30,10,utf8_decode('Grupo'),1,0,'C',true);
$pdf->Cell(50,10,utf8_decode('Promedio'),1,1,'C',true);

$pdf->SetFont('Arial','',10);

if(empty($materias)) {
    $pdf->Cell(190,10,utf8_decode('No hay materias inscritas.'),1,1,'C');
} else {
    foreach($materias as $m) {
        // Calcular promedio
        $misNotas = $notas[$m['id']] ?? [];
        $promedio = '-';
        if (count($misNotas) > 0) {
            $sum = array_sum($misNotas);
            $promedio = number_format($sum / count($misNotas), 1);
        }

        $pdf->Cell(30,10,utf8_decode($m['codigo']),1,0,'C');
        $pdf->Cell(80,10,utf8_decode($m['nombre']),1,0,'L');
        $pdf->Cell(30,10,utf8_decode($m['grupo']),1,0,'C');
        
        // Color condicional (Rojo reprobado, Verde aprobado)
        if($promedio != '-' && $promedio < 70) $pdf->SetTextColor(200,0,0);
        else if($promedio != '-') $pdf->SetTextColor(0,128,0);
        
        $pdf->Cell(50,10,$promedio,1,1,'C');
        $pdf->SetTextColor(0,0,0); // Reset color
    }
}

$pdf->Output('I', 'Kardex_'.$alumnoId.'.pdf'); // 'I' para mostrar en navegador
?>