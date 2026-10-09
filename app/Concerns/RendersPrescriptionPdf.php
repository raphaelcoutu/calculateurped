<?php

namespace App\Concerns;

use Carbon\Carbon;
use Illuminate\Http\Response;

trait RendersPrescriptionPdf
{
    /** @param array<string, mixed> $data */
    protected function renderPrescriptionPdf(array $data): Response
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadView('pdf.main', $data);

        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(150, 760, 'Signature du prescripteur', null, 9, [0.3, 0.3, 0.3]);
        $canvas->page_text(330, 760, 'No de permis', null, 9, [0.3, 0.3, 0.3]);
        $canvas->page_text(470, 760, 'Date', null, 9, [0.3, 0.3, 0.3]);
        $canvas->page_text(530, 760, 'hre', null, 9, [0.3, 0.3, 0.3]);
        $canvas->page_text(555, 760, 'min', null, 9, [0.3, 0.3, 0.3]);
        $canvas->page_text(470, 745, '/        /                  :', null, 11, [0.3, 0.3, 0.3]);
        $canvas->page_line(80, 760, 400, 760, [0, 0, 0], 1);
        $canvas->page_line(430, 760, 580, 760, [0, 0, 0], 1);
        $canvas->page_text(40, 770, Carbon::now()->toDateTimeString(), null, 10, [0, 0, 0]);
        $canvas->page_text(520, 770, 'Page {PAGE_NUM} de {PAGE_COUNT}', null, 10, [0, 0, 0]);

        return $pdf->stream();

    }
}
