<?php

namespace App\Helper;

use Milon\Barcode\DNS1D;
use Milon\Barcode\Facades\DNS1DFacade;

class GenerateBarcode
{
    public static function generateBarcode($code)
    {
        return DNS1DFacade::getBarcodePNG($code, 'C39+', 2, 50, [0, 0, 0], false);
    }
}
