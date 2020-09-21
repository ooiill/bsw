<?php

namespace App\Controller\BackendCover;

use App\Controller\AcmeBackendController;
use Leon\BswBundle\Controller as Bsw;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

class BswMixed extends AcmeBackendController
{
    use Bsw\BswMixed\CleanBackend;
    use Bsw\BswMixed\EnumDict;
    use Bsw\BswMixed\Export;
    use Bsw\BswMixed\Language;
    use Bsw\BswMixed\NumberCaptcha;
    use Bsw\BswMixed\SiteIndex;
    use Bsw\BswMixed\Telegram;
    use Bsw\BswMixed\ThirdMessage;
}