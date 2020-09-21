<?php

namespace App\Controller;

use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Controller\BswMixed\Upload;

/**
 * Mixed controller
 */
class MixedWebController extends AcmeWebController
{
    use Upload;

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
        return $options;
    }
}