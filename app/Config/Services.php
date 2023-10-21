<?php

namespace Config;

use CodeIgniter\Config\BaseService;

use Dompdf\Dompdf;
use Dompdf\Options;

class Services extends BaseService
{
    public static function dompdf($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('dompdf');
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', false);
        $options->set('isJavascriptEnabled', true);
        // Tambahkan opsi lain yang diperlukan di sini

        $dompdf = new Dompdf($options);

        return $dompdf;
    }
}
