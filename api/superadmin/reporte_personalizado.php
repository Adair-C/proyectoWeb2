<?php
require_once "../../src/Database.php";
require_once "../../src/Auth.php";
require_once "../../src/fpdf/fpdf.php";

if (!Auth::isLoggedIn() || Auth::rol() !== 'superadmin') exit("Acceso denegado");

$tipo = $_GET['tipo'] ?? 'usuarios'; 
$filtro = $_GET['filtro'] ?? 'todos'; 
$idExtra = $_GET['id'] ?? 0;          

$pdo = Database::pdo();
$tituloReporte = "Reporte General";
$data = [];
$columnas = [];

// --- LÓGICA DE SELECCIÓN ---

if ($tipo === 'usuarios') {
    // REPORTE DE USUARIOS
    if ($filtro === 'todos') {
        $sql = "SELECT username, nombre_completo, email, rol, activo FROM usuarios ORDER BY rol, nombre_completo";
        $tituloReporte = "Reporte de Todos los Usuarios";
        $stmt = $pdo->query($sql);
    } else {
        $sql = "SELECT username, nombre_completo, email, rol, activo FROM usuarios WHERE rol = ? ORDER BY nombre_completo";
        $tituloReporte = "Reporte de " . ucfirst($filtro) . "s";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$filtro]);
    }
    $data = $stmt->fetchAll();
    
    // Configurar Columnas
    $columnas = [
        ['t' => 'Usuario', 'w' => 40],
        ['t' => 'Nombre', 'w' => 70],
        ['t' => 'Email', 'w' => 50],
        ['t' => 'Rol', 'w' => 30]
    ];

} elseif ($tipo === 'materias') {
    // REPORTE DE MATERIAS
    if ($idExtra > 0) {
        // Materias de un maestro específico
        $sql = "SELECT m.codigo, m.nombre, m.grupo, m.unidades 
                FROM materias m
                JOIN asignacion_maestro_materia amm ON m.id = amm.materia_id
                WHERE amm.maestro_id = ? ORDER BY m.nombre";
        
        // Obtener nombre del profe para el título
        $stmtName = $pdo->prepare("SELECT nombre_completo FROM usuarios WHERE id = ?");
        $stmtName->execute([$idExtra]);
        $profe = $stmtName->fetchColumn();
        
        $tituloReporte = "Materias asignadas a: " . utf8_decode($profe);
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idExtra]);
    } else {
        // Todas las materias
        $sql = "SELECT codigo, nombre, grupo, unidades FROM materias ORDER BY nombre";
        $tituloReporte = "Catálogo Completo de Materias";
        $stmt = $pdo->query($sql);
    }
    $data = $stmt->fetchAll();

    // Configurar Columnas
    $columnas = [
        ['t' => 'Codigo', 'w' => 30],
        ['t' => 'Materia', 'w' => 100],
        ['t' => 'Grupo', 'w' => 30],
        ['t' => 'Unidades', 'w' => 30]
    ];
}

// --- GENERACIÓN DEL PDF ---

class PDF extends FPDF {
    public $titulo;
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
$pdf->titulo = $tituloReporte;
$pdf->AliasNbPages();
$pdf->AddPage();

// PINTAR CABECERA DE TABLA
$pdf->SetFillColor(230,230,230);
$pdf->SetFont('Arial','B',10);

foreach ($columnas as $col) {
    $pdf->Cell($col['w'], 10, utf8_decode($col['t']), 1, 0, 'C', true);
}
$pdf->Ln();

// PINTAR DATOS
$pdf->SetFont('Arial','',10);

if (empty($data)) {
    $pdf->Cell(0, 10, 'No se encontraron registros para este reporte.', 1, 1, 'C');
} else {
    foreach ($data as $row) {
    
        $valores = array_values($row); 
        
        if ($tipo === 'usuarios') {
            $pdf->Cell(40, 10, utf8_decode($row['username']), 1);
            $pdf->Cell(70, 10, utf8_decode($row['nombre_completo']), 1);
            $pdf->Cell(50, 10, utf8_decode($row['email']), 1);
            $pdf->Cell(30, 10, ucfirst($row['rol']), 1, 0, 'C');
        } elseif ($tipo === 'materias') {
            $pdf->Cell(30, 10, utf8_decode($row['codigo']), 1);
            $pdf->Cell(100, 10, utf8_decode($row['nombre']), 1);
            $pdf->Cell(30, 10, $row['grupo'], 1, 0, 'C');
            $pdf->Cell(30, 10, $row['unidades'], 1, 0, 'C');
        }
        $pdf->Ln();
    }
}

$pdf->Output('I', 'Reporte.pdf');
?>