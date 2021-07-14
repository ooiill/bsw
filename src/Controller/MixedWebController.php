<?php

namespace App\Controller;

use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Document bsw
     *
     * @Route("/skills/{name}", name="app_skills_document", requirements={"name": "[a-zA-Z0-9\-\.]+"}, defaults={"name": "01.docker"})
     *
     * @param string $name
     *
     * @return Response
     * @throws
     */
    public function document(string $name): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $this->appendSrcCssWithKey('markdown', Abs::CSS_MARKDOWN);
        $this->appendSrcCssWithKey('highlight', Abs::CSS_HIGHLIGHT);
        $this->appendSrcCssWithKey('scroll', 'diy:scroll');
        $this->appendSrcJsWithKey('highlight', Abs::JS_HIGHLIGHT);

        $this->seoWithAppName = false;
        $markdown = $this->markdownDirectoryParse(
            $name,
            $this->getPath('mixed/skills', false),
        );

        // $this->cnf->font_symbol = null; // Not load iconfont.js

        return $this->showPage(
            [
                'args' => compact('name'),
                'data' => ['data' => $markdown],
            ],
            [],
            'layout/document.html'
        );
    }
}