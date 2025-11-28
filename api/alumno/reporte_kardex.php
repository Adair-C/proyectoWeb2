<?php
require_once "../../app/Config/Database.php";
require_once "../../app/Helpers/Auth.php";
require_once "../../app/Libs/fpdf/fpdf.php";

if (!Auth::isLoggedIn() || Auth::rol() !== 'alumno') exit("Acceso denegado");

$alumnoId = Auth::userId();
$nombreAlumno = Auth::nombreCompleto();
$pdo = Database::pdo();

// 1. Obtener materias y notas
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
  
        $logoFile = '../../public/assets/img/logo.png';

        if(file_exists($logoFile)){
            $this->Image($logoFile, 10, 8, 25); 
        }

        $this->SetFont('Arial','B',16);
        
        $this->Cell(30); 
        $this->Cell(0,10,utf8_decode('Sistema de Control Escolar'),0,1,'L');
        
        $this->SetFont('Arial','',10);
        $this->Cell(30);
        $this->Cell(0,5,utf8_decode('Reporte Oficial de Calificaciones'),0,1,'L');
        
        $this->SetDrawColor(111, 66, 193); 
        $this->SetLineWidth(1);
        $this->Line(10, 35, 285, 35); 
        $this->Ln(15);
    }
    function Footer() {
    $this->SetY(-20);
    
    $this->SetDrawColor(200, 200, 200);

    $anchoPagina = $this->GetPageWidth();
    $this->Line(10, $this->GetY(), $anchoPagina - 10, $this->GetY());
    
    $this->Ln(4); 

    $this->SetFont('Arial', 'I', 8);
    $this->SetTextColor(128, 128, 128); 

    $this->Cell(0, 10, utf8_decode('Sistema de Control Escolar - Documento Oficial'), 0, 0, 'L');
    $this->SetX(-30); 
    $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
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
        $pdf->SetTextColor(0,0,0); 
    }
}

$pdf->Output('I', 'Kardex_'.$alumnoId.'.pdf'); 
?>