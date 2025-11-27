<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/fpdf/fpdf.php"; 

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') exit("Acceso denegado");

$materiaId = $_GET['materia_id'] ?? 0;
$maestroId = Auth::userId();
$pdo = Database::pdo();

// 1. Validar que la materia pertenezca al maestro
$sqlValidar = "SELECT count(*) FROM asignacion_maestro_materia WHERE maestro_id = ? AND materia_id = ?";
$stmt = $pdo->prepare($sqlValidar);
$stmt->execute([$maestroId, $materiaId]);
if($stmt->fetchColumn() == 0) exit("No tienes permiso para ver esta materia.");

// 2. Obtener datos de la materia
$stmt = $pdo->prepare("SELECT nombre, grupo, codigo, unidades FROM materias WHERE id = ?");
$stmt->execute([$materiaId]);
$materia = $stmt->fetch();

// 3. Obtener Alumnos
$sqlAlumnos = "SELECT u.id, u.nombre_completo, u.username 
               FROM usuarios u 
               JOIN inscripciones i ON u.id = i.alumno_id 
               WHERE i.materia_id = ? AND u.rol = 'alumno'
               ORDER BY u.nombre_completo";
$stmt = $pdo->prepare($sqlAlumnos);
$stmt->execute([$materiaId]);
$alumnos = $stmt->fetchAll();

// 4. Obtener Calificaciones
$sqlNotas = "SELECT alumno_id, unidad, calificacion FROM calificaciones WHERE materia_id = ?";
$stmt = $pdo->prepare($sqlNotas);
$stmt->execute([$materiaId]);
$notasRaw = $stmt->fetchAll();

$notas = [];
foreach($notasRaw as $row) {
    $notas[$row['alumno_id']][$row['unidad']] = $row['calificacion'];
}

// --- GENERACIÓN DEL PDF ---

class PDF extends FPDF {
    function Header() {
        // Logo opcional: $this->Image('logo.png',10,6,30);
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,utf8_decode('Reporte de Calificaciones'),0,1,'C');
        $this->Ln(2);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4'); 
$pdf->AliasNbPages();
$pdf->AddPage();

// Info de la Materia
$pdf->SetFont('Arial','',12);
$pdf->Cell(25,10,utf8_decode('Materia:'),0,0);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,10,utf8_decode($materia['nombre'] . ' (' . $materia['codigo'] . ')'),0,0);

$pdf->SetFont('Arial','',12);
$pdf->Cell(20,10,utf8_decode('Grupo:'),0,0);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(30,10,utf8_decode($materia['grupo']),0,1);
$pdf->Ln(5);

// --- TABLA ---
$pdf->SetFillColor(230,230,230);
$pdf->SetFont('Arial','B',10);

// Cabecera Estática
$pdf->Cell(10,10,'#',1,0,'C',true);
$pdf->Cell(80,10,'Nombre del Alumno',1,0,'L',true);

// Cabecera Dinámica (U1, U2...)
$anchoUnidad = 20;
for($i=1; $i <= $materia['unidades']; $i++) {
    $pdf->Cell($anchoUnidad,10,'U'.$i,1,0,'C',true);
}
// Promedio
$pdf->Cell(25,10,'Promedio',1,1,'C',true);

// --- DATOS ---
$pdf->SetFont('Arial','',10);
$count = 1;

foreach($alumnos as $al) {
    $pdf->Cell(10,10,$count++,1,0,'C');
    $pdf->Cell(80,10,utf8_decode($al['nombre_completo']),1,0,'L');
    
    $suma = 0;
    $calificadas = 0;

    // Pintar notas
    for($i=1; $i <= $materia['unidades']; $i++) {
        $val = isset($notas[$al['id']][$i]) ? $notas[$al['id']][$i] : '-';
        
        // Calcular promedio solo con lo calificado
        if($val !== '-') {
            $suma += $val;
            $calificadas++;
            
            // Color rojo si reprobó unidad
            if($val < 70) $pdf->SetTextColor(200,0,0);
        }
        
        $pdf->Cell($anchoUnidad,10,$val,1,0,'C');
        $pdf->SetTextColor(0,0,0); // Reset color
    }

    // Calcular Promedio Final
    $prom = '-';
    if($calificadas > 0) {
        $prom = number_format($suma / $calificadas, 1);
    }
    
    // Negrita para promedio
    $pdf->SetFont('Arial','B',10);
    if($prom !== '-' && $prom < 70) $pdf->SetTextColor(200,0,0);
    else if($prom !== '-') $pdf->SetTextColor(0,128,0);

    $pdf->Cell(25,10,$prom,1,1,'C');
    
    // Reset estilos para siguiente fila
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','',10);
}

$pdf->Output('I', 'Reporte_'.$materia['codigo'].'.pdf');
?>