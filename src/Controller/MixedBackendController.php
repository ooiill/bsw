<?php

namespace App\Controller;

use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Controller\BswMixed\Upload;
use Leon\BswBundle\Controller\BswMixed\EnumDict;

/**
 * Mixed controller
 */
class MixedBackendController extends AcmeBackendController
{
    use Upload;
    use EnumDict;

    /**
     * Upload options handler
     *
     * @param string $flag
     * @param array  $options
     *
     * @return array
     */
    public function uploadOptionsHandler(string $flag, array $options): array
    {
        return parent::uploadOptionsHandler($flag, $options);
    }
}