<?php

namespace App\Controller\BackendCover;

use App\Controller\AcmeBackendController;
use Leon\BswBundle\Controller as Bsw;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

class BswAdminRoleAccessControl extends AcmeBackendController
{
    use Bsw\BswAdminRoleAccessControl\Common;
    use Bsw\BswAdminRoleAccessControl\Preview;
    use Bsw\BswAdminRoleAccessControl\Persistence;
    use Bsw\BswAdminRoleAccessControl\Grant;
}