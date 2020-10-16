<?php

namespace App\Controller;

use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

/**
 * Homepage controller
 */
class HomeBackendController extends AcmeBackendController
{
    /**
     * @return array
     */
    public function homepageAnnotation()
    {
        return [
            'id'    => [
                'width'  => 80,
                'align'  => Abs::POS_CENTER,
                'render' => Abs::RENDER_CODE,
            ],
            'name'  => [
                'width'  => 200,
                'align'  => Abs::POS_RIGHT,
                'render' => Abs::RENDER_CODE,
            ],
            'value' => [
                'width' => 500,
                'html'  => true,
            ],
        ];
    }

    /**
     * @return string
     */
    public function homepageWelcome(): string
    {
        return "→ NICE TO MEET YOU.";
    }

    /**
     * @return array
     */
    public function homepagePreviewData(): array
    {
        $github = 'https://github.com/ooiill/bsw';

        return [
            ['id' => 1, 'name' => 'Time', 'value' => date(Abs::FMT_FULL)],
            ['id' => 2, 'name' => 'Version', 'value' => $this->parameter('version')],
            [
                'id'    => 3,
                'name'  => 'Github',
                'value' => Html::tag('a', $github, ['target' => '_blank', 'href' => $github]),
            ],
        ];
    }

    /**
     * Homepage
     *
     * @Route("/backend", name="backend_homepage")
     * @Access()
     *
     * @return Response
     */
    public function homepage(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}