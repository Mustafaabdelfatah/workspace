<?php
namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class PdfGeneratorRepository
{
    private $mpdf;

    public function __construct()
    {
        $this->mpdf = new \Mpdf\Mpdf([
            'PDFA' => false,
            'PDFAauto' => false,
            'debug' => true,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => storage_path('app/mpdf-tmp'),
            'fontDir' => [
                base_path('resources/fonts')
            ],
            'fontdata' => [
                'montserrat' => [
                    'R' => 'Montserrat-Arabic-Light.ttf',
                    'M' => 'Montserrat-Arabic-Medium.ttf',
                    'I' => 'Montserrat-Arabic-Light.ttf',
                    'BI' => 'Montserrat-Arabic-Light.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ]
        ]);
    }

    /**
     * Initiate pdf
     *
     * @param mixed $args
     *
     * @return mixed
     */
    public function initiatePdf(mixed $view): mixed
    {
        $this->mpdf->WriteHTML($view);

        return base64_encode($this->mpdf->Output('', 'S'));
    }

    /**
     * store pdf
     *
     * @param mixed $args
     *
     * @return mixed
     */
    public function storePdf(mixed $view, string $folder): mixed
    {
        $this->mpdf->WriteHTML($view);

        $pdf = $this->mpdf->Output('', 'S');
        $filePath = $folder . '/' . Str::random(40) . '.pdf';
        Storage::put($filePath, $pdf);

        return $filePath;
    }
}
