<?php

namespace App\Http\Controllers;
use PDF;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function pdfabogados()
    {
    $abogados = \App\Models\Lawyer::orderBy('created_at', 'DESC')->with('persona')->get();
     $pdf = PDF::loadView('vista_pdf_abo', ['data' => $abogados]);
    //return $pdf->stream();
     return $pdf->download('reportes_abogados.pdf');
    } 
}
