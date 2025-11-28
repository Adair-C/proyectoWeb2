<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/fpdf/fpdf.php"; 

if (!Auth::isLoggedIn() || Auth::rol() !== 'maestro') exit("Acceso denegado");

$materiaId = $_GET['id'] ?? 0;
$filtro = $_GET['filtro'] ?? 'todos'; 
$maestroId = Auth::userId();
$pdo = Database::pdo();

// 1. SEGURIDAD
$sqlPermiso = "SELECT COUNT(*) FROM asignacion_maestro_materia WHERE maestro_id = ? AND materia_id = ?";
$stmt = $pdo->prepare($sqlPermiso);
$stmt->execute([$maestroId, $materiaId]);
if ($stmt->fetchColumn() == 0) exit("No tienes permiso para generar reportes de esta materia.");

// 2. DATOS MATERIA
$stmt = $pdo->prepare("SELECT nombre, grupo, codigo, unidades FROM materias WHERE id = ?");
$stmt->execute([$materiaId]);
$materia = $stmt->fetch();

// 3. ALUMNOS Y NOTAS
$sqlAlumnos = "SELECT u.id, u.nombre_completo 
               FROM usuarios u 
               JOIN inscripciones i ON u.id = i.alumno_id 
               WHERE i.materia_id = ? AND u.rol = 'alumno'
               ORDER BY u.nombre_completo ASC";
$stmt = $pdo->prepare($sqlAlumnos);
$stmt->execute([$materiaId]);
$alumnos = $stmt->fetchAll();

$sqlNotas = "SELECT alumno_id, unidad, calificacion FROM calificaciones WHERE materia_id = ?";
$stmt = $pdo->prepare($sqlNotas);
$stmt->execute([$materiaId]);
$notasRaw = $stmt->fetchAll();

$notas = [];
foreach($notasRaw as $row) {
    $notas[$row['alumno_id']][$row['unidad']] = $row['calificacion'];
}

// --- CONFIGURACIÓN PDF ---

class PDF extends FPDF {
    public $subtitulo;
    
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

// Definir texto del subtítulo según el filtro
$textoFiltro = "Listado General";
if ($filtro === 'aprobados') $textoFiltro = "Solo Alumnos Aprobados (Promedio >= 70)";
if ($filtro === 'reprobados') $textoFiltro = "Solo Alumnos Reprobados (Promedio < 70)";

$pdf = new PDF('L', 'mm', 'A4');
$pdf->subtitulo = $textoFiltro;
$pdf->AliasNbPages();
$pdf->AddPage();

// Info Materia
$pdf->SetFont('Arial','',12);
$pdf->Cell(20,10,utf8_decode('Materia:'),0,0);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,10,utf8_decode($materia['nombre'] . ' (' . $materia['codigo'] . ')'),0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(15,10,utf8_decode('Grupo:'),0,0);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(30,10,utf8_decode($materia['grupo']),0,1);
$pdf->Ln(5);

// Cabeceras
$pdf->SetFillColor(230,230,230);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,10,'#',1,0,'C',true);
$pdf->Cell(90,10,'Nombre del Alumno',1,0,'L',true);

$unidades = $materia['unidades'];
$anchoU = 20;
for($i=1; $i <= $unidades; $i++) {
    $pdf->Cell($anchoU, 10, 'U'.$i, 1, 0, 'C', true);
}
$pdf->Cell(25, 10, 'Promedio', 1, 1, 'C', true);

// Datos
$pdf->SetFont('Arial','',10);
$count = 1;
$alumnosImpresos = 0; 

foreach($alumnos as $al) {
    // 1. Calcular promedio primero para filtrar
    $suma = 0;
    $calificadas = 0;
    
    // Guardamos las notas temporales para imprimirlas después si pasa el filtro
    $notasAlumno = []; 

    for($i=1; $i <= $unidades; $i++) {
        $val = isset($notas[$al['id']][$i]) ? $notas[$al['id']][$i] : '-';
        $notasAlumno[$i] = $val;
        
        if($val !== '-') {
            $suma += $val;
            $calificadas++;
        }
    }

    $promedioNum = ($calificadas > 0) ? ($suma / $calificadas) : 0;
    $promedioTexto = ($calificadas > 0) ? number_format($promedioNum, 1) : '-';

    // 2. APLICAR FILTRO
    if ($filtro === 'aprobados') {

        if ($calificadas === 0 || $promedioNum < 70) continue;
    }
    if ($filtro === 'reprobados') {
        if ($calificadas > 0 && $promedioNum >= 70) continue;
    }

    // 3. IMPRIMIR FILA (Si pasó el filtro)
    $pdf->Cell(10,10,$count++,1,0,'C');
    $pdf->Cell(90,10,utf8_decode($al['nombre_completo']),1,0,'L');

    // Imprimir notas guardadas
    for($i=1; $i <= $unidades; $i++) {
        $val = $notasAlumno[$i];
        if($val !== '-' && $val < 70) $pdf->SetTextColor(200,0,0); // Rojo
        $pdf->Cell($anchoU,10,$val,1,0,'C');
        $pdf->SetTextColor(0);
    }

    // Imprimir promedio final
    $pdf->SetFont('Arial','B',10);
    if($promedioTexto !== '-' && $promedioNum < 70) $pdf->SetTextColor(200,0,0);
    else if($promedioTexto !== '-') $pdf->SetTextColor(0,128,0);

    $pdf->Cell(25,10,$promedioTexto,1,1,'C');
    
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial','',10);
    
    $alumnosImpresos++;
}

if ($alumnosImpresos === 0) {
    $pdf->Ln();
    $pdf->Cell(0, 15, utf8_decode('No se encontraron alumnos con este criterio.'), 1, 1, 'C');
}

$pdf->Output('I', 'Reporte_'.$filtro.'.pdf');
?>