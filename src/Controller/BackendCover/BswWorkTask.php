<?php

namespace App\Controller\BackendCover;

use App\Controller\AcmeBackendController;
use Leon\BswBundle\Controller as Bsw;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

class BswWorkTask extends AcmeBackendController
{
    use Bsw\BswWorkTask\Common;
    use Bsw\BswWorkTask\Preview;
    use Bsw\BswWorkTask\Persistence;
    use Bsw\BswWorkTask\Close;
    use Bsw\BswWorkTask\Notes;
    use Bsw\BswWorkTask\PersistenceSimple;
    use Bsw\BswWorkTask\Progress;
    use Bsw\BswWorkTask\Transfer;
    use Bsw\BswWorkTask\WeekReport;
    use Bsw\BswWorkTask\Weight;
}