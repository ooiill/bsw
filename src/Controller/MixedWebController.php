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
     * @Route("/md/{name}", name="app_md_document", requirements={"name": "[a-zA-Z0-9\-\.]+"}, defaults={"name": "01.docker"})
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
        $this->appendSrcCssWithKey('fancy-box', Abs::CSS_FANCY_BOX);

        $this->appendSrcJsWithKey('highlight', Abs::JS_HIGHLIGHT);
        $this->appendSrcJsWithKey('highlight-ln', Abs::JS_HIGHLIGHT_LN);
        $this->appendSrcJsWithKey('fancy-box', Abs::JS_FANCY_BOX);

        $this->seoWithAppName = false;
        $markdown = $this->markdownDirectoryParse(
            $name,
            $this->getPath('mixed/markdown', false),
            'light',
        );

        $this->seoTitle = 'ooiill 技能知识点';

        // $this->cnf->font_symbol = null; // Not load iconfont.js

        return $this->showPage(
            [
                'args' => compact('name'),
                'data' => ['data' => $markdown],
            ],
            [],
            'layout/document-layout.html'
        );
    }
}