<?php

namespace App\Controller\BackendCover;

use App\Controller\AcmeBackendController;
use Leon\BswBundle\Controller as Bsw;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

class BswAdminUser extends AcmeBackendController
{
    use Bsw\BswAdminUser\Common;
    use Bsw\BswAdminUser\Preview;
    use Bsw\BswAdminUser\Persistence;
    use Bsw\BswAdminUser\Login;
    use Bsw\BswAdminUser\Logout;
    use Bsw\BswAdminUser\Profile;
}