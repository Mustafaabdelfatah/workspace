<?php declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Support\Facades\File;

class StampService
{
    public function generateStamp($title, $companyNameEn, $companyNameAr, $content, $logoPublicPath = '', $asImage = false)
    {
        if (empty($logoPublicPath) || !File::exists($logoPublicPath)) {
            $logoPublicPath = public_path('assets/nahidh/logo.png');
        }
        $svgPath = public_path('stamps/stamp.svg');
        $arabicFontPath = base_path('resources/fonts/ge_dinar_two_medium.otf');
        $englishFontPath = base_path('resources/fonts/cg-omega-bold.ttf');

        // Load and clean SVG content
        $svgContent = File::get($svgPath);
        $svgContent = preg_replace('/^\xEF\xBB\xBF/', '', $svgContent);
        $svgContent = ltrim($svgContent);

        $svg = simplexml_load_string($svgContent);
        if ($svg === false) {
            abort(500, 'Failed to load SVG');
        }
        // Embed custom font as Base64
        $arabicFontData = base64_encode(File::get($arabicFontPath));
        $englishFontData = base64_encode(File::get($englishFontPath));
        $style = <<<CSS
            @font-face {
                font-family: 'ge_dinar_two_medium';
                src: url('data:font/opentype;charset=utf-8;base64,{$arabicFontData}') format('opentype');
            }
            @font-face {
                font-family: 'cg-omega-bold';
                src: url('data:font/truetype;charset=utf-8;base64,{$englishFontData}') format('truetype');
            }
            CSS;

        $defs = $svg->addChild('defs');
        $styleNode = $defs->addChild('style', $style);
        $styleNode->addAttribute('type', 'text/css');

        $titleWidth = 0;
        $frameX = 490;
        if (!empty($title)) {
            $title = trim($title);
            $titleFontSize = 30;
            $titleBox = imagettfbbox($titleFontSize, 0, $arabicFontPath, $title);

            $factor = config('app.app_on_server') ? 0.84 : 0.7;
            $titleWidth = abs($titleBox[2] - $titleBox[0]) * $factor;
            if ($titleWidth > 320) {
                throw new \Exception(__('core::messages.stamp_title_too_long'));
            }

            // title part
            $frameX = ($frameX - $titleWidth);
            $group = $svg->addChild('g');
            $group->addAttribute('transform', 'translate(' . $frameX . ', 20)');

            $text = $group->addChild('text', $title);
            $text->addAttribute('x', '0');
            $text->addAttribute('y', '0');
            $text->addAttribute('font-size', (string) $titleFontSize);
            $text->addAttribute('fill', '#2a3583');
            $text->addAttribute('style', 'font-family: ge_dinar_two_medium;');
            $frameX -= 12;
        }

        // top polygon
        $topPolyStartX = $frameX;
        $topPolyY = 12.91;

        // $topPolylineNewX = $topPolyStartX - $titleWidth;
        $svg->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
        $polylines = $svg->xpath('//svg:polyline');
        $polyline = $polylines[0];
        $polyline['points'] = "{$topPolyStartX} {$topPolyY} 122.94 {$topPolyY} 113.61 22.68";

        // company arabic name

        $companyNameArFontSize = 25;
        $companyNameEnFontSize = 20;

        $companyNameFrameX = 140;
        $companyNameGroup = $svg->addChild('g');
        $companyNameGroup->addAttribute('transform', 'translate(' . $companyNameFrameX . ', 40)');

        $reachWidth = 360;
        $rectHeight = 30;
        $rect = $companyNameGroup->addChild('rect');
        $rect->addAttribute('x', '0');
        $rect->addAttribute('y', '0');
        $rect->addAttribute('width', (string) $reachWidth);
        $rect->addAttribute('height', (string) $rectHeight);
        $rect->addAttribute('fill', 'transparent');
        $rect->addAttribute('direction', 'rtl');

        if (strlen($companyNameAr) > 0) {
            $text = $companyNameGroup->addChild('text', $companyNameAr);
            $text->addAttribute('x', '180');
            $text->addAttribute('y', '10');
            $text->addAttribute('font-size', (string) $companyNameArFontSize);
            $text->addAttribute('text-anchor', 'middle');
            $text->addAttribute('dominant-baseline', 'middle');
            $text->addAttribute('fill', '#2a3583');
            $text->addAttribute('style', 'font-family: ge_dinar_two_medium;');
        }

        // company english name
        if (strlen($companyNameEn) > 0) {
            $text = $companyNameGroup->addChild('text', $companyNameEn);
            $text->addAttribute('x', '180');
            $text->addAttribute('y', '40');
            $text->addAttribute('font-size', (string) $companyNameEnFontSize);
            $text->addAttribute('text-anchor', 'middle');
            $text->addAttribute('dominant-baseline', 'middle');
            $text->addAttribute('fill', '#2a3583');
            $text->addAttribute('font-weight', 'bold');
            $text->addAttribute('letter-spacing', '.7px');
            $text->addAttribute('style', 'font-family: cg-omega-bold;');
        }

        // content part

        if (count($content) > 0) {
            $contentY = 270;
            foreach (array_reverse($content) as $key => $value) {
                $contentX = 500;
                $contentFontSize = 23;
                $text = $svg->addChild('text', $value);
                $text->addAttribute('x', (string) $contentX);
                $text->addAttribute('y', (string) $contentY);
                $text->addAttribute('font-size', (string) $contentFontSize);
                $text->addAttribute('fill', '#2a3583');
                $text->addAttribute('direction', 'rtl');
                $text->addAttribute('style', 'font-family: ge_dinar_two_medium;');
                $contentY -= 30;
            }
        }

        // logo part

        $imgData = base64_encode(file_get_contents($logoPublicPath));
        $image = $svg->addChild('image', null, 'http://www.w3.org/2000/svg');
        $image->addAttribute('x', '-85');
        $image->addAttribute('y', '-80');
        $image->addAttribute('width', '300');
        $image->addAttribute('height', '300');

        $image->addAttribute(
            'xlink:href',
            'data:image/png;base64,' . $imgData,
            'http://www.w3.org/1999/xlink'
        );

        $output = $svg->asXML();
        $output = preg_replace('/<\?xml.*?\?>\s*/', '', $output);

        return $this->svgToImageResponse($output);
    }

    protected function svgToImageResponse(string $svgContent)
    {
        // Create temporary files
        $tmpSvg = tempnam(sys_get_temp_dir(), 'stamp_') . '.svg';
        $tmpPng = tempnam(sys_get_temp_dir(), 'stamp_') . '.png';

        file_put_contents($tmpSvg, $svgContent);

        // Use rsvg-convert (Ubuntu package librsvg2-bin)
   
        exec("rsvg-convert -o {$tmpPng} {$tmpSvg}");
        // exec("C:/msys64/ucrt64/bin/rsvg-convert -o {$tmpPng} {$tmpSvg}");

        $imageData = file_get_contents($tmpPng);

        // Clean up temp files
        @unlink($tmpSvg);
        @unlink($tmpPng);

        return base64_encode($imageData);
    }
}
