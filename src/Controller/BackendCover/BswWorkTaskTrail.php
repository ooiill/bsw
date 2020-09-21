<?php

namespace App\Controller\BackendCover;

use App\Controller\AcmeBackendController;
use Leon\BswBundle\Controller as Bsw;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

class BswWorkTaskTrail extends AcmeBackendController
{
    use Bsw\BswWorkTaskTrail\Common;
    use Bsw\BswWorkTaskTrail\Preview;
    use Bsw\BswWorkTaskTrail\Persistence;
}