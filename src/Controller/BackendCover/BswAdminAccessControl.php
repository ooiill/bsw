<?php

namespace App\Controller\BackendCover;

use App\Controller\AcmeBackendController;
use Leon\BswBundle\Controller as Bsw;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

class BswAdminAccessControl extends AcmeBackendController
{
    use Bsw\BswAdminAccessControl\Common;
    use Bsw\BswAdminAccessControl\Preview;
    use Bsw\BswAdminAccessControl\Persistence;
    use Bsw\BswAdminAccessControl\Grant;
}