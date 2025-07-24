<?php
require_once 'Vacaciones.php';

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'errores.log');

function generarPDF($link, $colaborador_id, $inicio, $fin)
{
    try {
        require_once '../TCPDF-main/tcpdf.php';
        $dias = (new DateTime($inicio))->diff(new DateTime($fin))->days + 1;

        // 🔽 Obtener datos del colaborador
        $stmt = $link->prepare("SELECT primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, identificacion FROM colaboradores WHERE id_colaborador = ?");
        $stmt->bind_param("i", $colaborador_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $colaborador = $result->fetch_assoc();

        $pdfs_dir = '../pdfs/';
        if (!file_exists($pdfs_dir)) {
            mkdir($pdfs_dir, 0777, true);
        }

        // 📄 Crear PDF
        $pdf = new TCPDF();
        $pdf->SetMargins(20, 30, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // 📌 Logo (ajusta ruta)
//        $logo = '../images/Logo.png';
//        if (file_exists($logo)) {
//            $pdf->Image($logo, 100, 15, 40);
//        }
//        $pdf->Ln(10);

        // 🎯 Título centrado
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Solicitud Formal de Vacaciones', 0, 1, 'C');
        $pdf->Ln(5);

        // ✍️ Introducción
        $pdf->SetFont('helvetica', '', 11);
        $mensaje = "Por medio del presente documento, se formaliza la solicitud de vacaciones del colaborador que se detalla a continuación. Este documento forma parte del expediente institucional de Recursos Humanos.";
        $pdf->MultiCell(0, 10, $mensaje, 0, 'J');
        $pdf->Ln(5);

        // 📋 Datos del colaborador
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Colaborador: ' . $colaborador['primer_nombre'] . ' ' . $colaborador['segundo_nombre'] . ' ' . $colaborador['primer_apellido'] . ' ' . $colaborador['segundo_apellido'], 0, 1);
        $pdf->Cell(0, 10, 'Cédula: ' . $colaborador['identificacion'], 0, 1);
        $pdf->Cell(0, 10, 'Fecha de inicio: ' . $inicio, 0, 1);
        $pdf->Cell(0, 10, 'Fecha de fin: ' . $fin, 0, 1);
        $pdf->Cell(0, 10, 'Días solicitados: ' . $dias, 0, 1);
        $pdf->Ln(9);

        // 🖊️ Firmas
        $pdf->Ln(15);
        $pdf->Cell(0, 10, '_______________________________', 0, 1, 'L');
        $pdf->Cell(0, 5, 'Firma del Colaborador', 0, 1, 'L');

        $pdf->Ln(15);
        $pdf->Cell(0, 10, '_______________________________', 0, 1, 'L');
        $pdf->Cell(0, 5, 'Firma de Recursos Humanos', 0, 1, 'L');

        // 📌 Código QR centrado (con datos de control)
        $qr = 'Colaborador: ' . $colaborador['primer_nombre'] . ' ' . $colaborador['primer_apellido'] . ' | Fecha: ' . date('Y-m-d H:i:s');
        $style = ['border' => 0, 'vpadding' => 'auto', 'hpadding' => 'auto', 'fgcolor' => [0, 0, 0], 'bgcolor' => false];
        $pdf->write2DBarcode($qr, 'QRCODE,H', 85, $pdf->GetY(), 40, 40, $style, 'N');
        $pdf->Ln(30);

        $pdf->Ln(2);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Documento generado automáticamente - Capital Humano', 0, 0, 'C');

        // 💾 Guardar PDF
        $filename = $pdfs_dir . 'pdfs' . $colaborador_id . '_' . date('Ymd_His') . '.pdf';
        $pdf->Output($filename, 'D');

    }catch (Exception $e){
        error_log($e->getMessage());
    }

}

