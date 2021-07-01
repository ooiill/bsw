<?php

namespace App\Controller;

use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw as BswModule;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Hook\Entity\MoneyStringify;
use App\Module\Entity\Enum;
use Leon\BswBundle\Controller\BswBackendController;
use stdClass;

class AcmeBackendController extends BswBackendController
{
    /**
     * @var string
     */
    public static $enum = Enum::class;

    /**
     * @var bool
     */
    protected $plaintextSensitive = true;

    /**
     * bootstrap
     */
    protected function bootstrap()
    {
        parent::bootstrap();

        $app = md5($this->cnf->app_name);
        $this->appendSrcJsWithKey($app, 'diy:app');
        $this->appendSrcCssWithKey($app, 'diy:app');

        $skin = $this->parameter('skin', '');
        $skin = Helper::underToCamel($skin, false);
        $skinFn = "skin{$skin}";
        if (method_exists($this, $skinFn)) {
            $this->{$skinFn}();
        }
    }

    /**
     * 皮肤 terse
     */
    protected function skinTerse()
    {
        $this->appendSrcCss('diy:skin-terse', Abs::POS_BOTTOM, 'cw');
        $this->logicMerge('display', [Abs::MODULE_CRUMBS]);
        if (strpos($this->route, 'app_bsw_work') === false) {
            $this->logicMerge('display', [Abs::MODULE_FOOTER]);
        }
        if (strpos($this->route, "app_") === 0) {
            $this->logicMerge('display', [Abs::MODULE_OPERATE]);
        }
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function hookerExtraArgs(array $args = []): array
    {
        return parent::hookerExtraArgs(
            [
                MoneyStringify::class => [
                    '_fields' => [],
                    'tpl'     => "￥%s",
                ],
            ]
        );
    }

    /**
     * Entity same hint
     *
     * @param object $options
     * @param object $item
     * @param string $table
     * @param string $action
     *
     * @return object
     */
    protected static function sameHint($options, $item, string $table, string $action)
    {
        return $options;
    }

    /**
     * Entity preview hint
     *
     * @param object   $item
     * @param string   $table
     * @param array    $fields
     * @param array    $args
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityPreviewHint($item, string $table, array $fields, array $args, stdClass $options = null)
    {
        $options = $options ?? new stdClass();
        $options = parent::entityPreviewHint($item, $table, $fields, $args, $options);

        // todo

        $options = self::sameHint($options, $item, $table, Abs::TAG_PREVIEW);

        return $options;
    }

    /**
     * Entity persistence hint
     *
     * @param object   $item
     * @param string   $table
     * @param array    $fields
     * @param array    $args
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityPersistenceHint(
        $item,
        string $table,
        array $fields,
        array $args,
        stdClass $options = null
    ) {
        $options = $options ?? new stdClass();
        $options = parent::entityPersistenceHint($item, $table, $fields, $args, $options);

        // todo

        $options = self::sameHint($options, $item, $table, Abs::TAG_PERSISTENCE);

        return $options;
    }

    /**
     * Entity filter hint
     *
     * @param object   $item
     * @param string   $table
     * @param array    $fields
     * @param array    $args
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityFilterHint($item, string $table, array $fields, array $args, stdClass $options = null)
    {
        $options = $options ?? new stdClass();
        $options = parent::entityFilterHint($item, $table, $fields, $args, $options);

        // todo

        $options = self::sameHint($options, $item, $table, Abs::TAG_FILTER);

        return $options;
    }

    /**
     * @param object $item
     * @param string $table
     * @param array  $fields
     *
     * @return mixed
     */
    public static function previewTailorHint($item, string $table, array $fields)
    {
        $result = parent::previewTailorHint($item, $table, $fields);
        if (!empty($result)) {
            return $result;
        }

        return null;
    }

    /**
     * @param object $item
     * @param string $table
     * @param array  $fields
     *
     * @return mixed
     */
    public static function persistenceTailorHint($item, string $table, array $fields)
    {
        $result = parent::persistenceTailorHint($item, $table, $fields);
        if (!empty($result)) {
            return $result;
        }

        return null;
    }

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
        $options = parent::uploadOptionsHandler($flag, $options);
        $imgMime = self::mergeMimeMaps(self::$imgSimpleMap);

        $appOptions = [
            'icon'  => array_merge(
                [
                    'maxSize'        => 128,
                    'picSizes'       => [[100, 1200], [100, 1200]],
                    'nonExistsForce' => true,
                ],
                $imgMime
            ),
            'cover' => array_merge(
                [
                    'maxSize'        => 256,
                    'picSizes'       => [[300, 2000], [300, 2000]],
                    'nonExistsForce' => true,
                ],
                $imgMime
            ),
            'x'     => array_merge(
                [
                    'maxSize'        => 300,
                    'picSizes'       => [[32, 1200], [32, 1200]],
                    'nonExistsForce' => true,
                ],
                $imgMime
            ),
        ];

        return array_merge($options, $appOptions[$flag] ?? []);
    }

    /**
     * @return Button
     */
    public function fullContentScreenButton(): Button
    {
        return (new Button($this->getArgs('screen') ? 'Exit full screen' : 'Enter full screen'))
            ->setIcon($this->cnf->icon_speech)
            ->setType(Abs::THEME_ELE_SUCCESS_OL)
            ->setClick('urlParamsTrigger')
            ->setArgs(
                [
                    'params' => [
                        'screen'        => 'content',
                        'bswClsContent' => 'bsw-no-margin bsw-no-border',
                    ],
                ]
            );
    }

    /**
     * @param int $height
     *
     * @return array
     */
    public function fullContentScreen(int $height = 180): array
    {
        $screen = $this->getArgs('screen') === 'content';
        if ($screen) {
            $this->appendSrcCss('diy:scroll');
            $this->logicMerge('display', [Abs::MODULE_MENU, Abs::MODULE_HEADER]);
        }

        return [
            BswModule\Preview\Module::class => [
                'scroll'         => $screen ? ['y' => "{var:WH.eleH - {$height}}"] : [],
                'size'           => $screen ? Abs::SIZE_SMALL : Abs::SIZE_DEFAULT,
                'paginationShow' => !$screen,
            ],
        ];
    }
}