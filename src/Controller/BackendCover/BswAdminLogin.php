<?php

namespace App\Controller\BackendCover;

use App\Controller\AcmeBackendController;
use Leon\BswBundle\Controller as Bsw;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

class BswAdminLogin extends AcmeBackendController
{
    use Bsw\BswAdminLogin\Common;
    use Bsw\BswAdminLogin\Preview;
    use Bsw\BswAdminLogin\Persistence;
}