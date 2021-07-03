<?php

namespace App\Controller;

use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Homepage controller
 */
class HomeWebController extends AcmeWebController
{
    /**
     * Homepage
     *
     * @Route("/", name="web_homepage")
     *
     * @return Response
     * @throws
     */
    public function homepage(): Response
    {
        if (($args = $this->valid(Abs::VW_LOGIN)) instanceof Response) {
            return $args;
        }

        /*
        $result = $this->service->post(
            'user/register',
            [
                'account'      => 18011112222,
                'password'     => '123456',
                'captcha'      => 9527,
            ]
        );
        dd($result, $this->service->pop(true));
        */

        // If you want custom the TDK, just do it
        // Otherwise obtain from `cnf`、`seo.lang.yaml` successively

        // $this->seoTitle = 'Custom website title';
        // $this->seoDescription = 'Custom website description';
        // $this->seoKeywords = 'Custom website keyword';

        return $this->showPage(
            [
                'rhetoric' => date(Abs::FMT_FULL),
            ]
        );
    }
}