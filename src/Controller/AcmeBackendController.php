<?php

namespace App\Controller;

use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Module\Entity\Abs;
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
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityPreviewHint($item, string $table, array $fields, stdClass $options = null)
    {
        $options = $options ?? new stdClass();
        $options = parent::entityPreviewHint($item, $table, $fields, $options);

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
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityPersistenceHint($item, string $table, array $fields, stdClass $options = null)
    {
        $options = $options ?? new stdClass();
        $options = parent::entityPersistenceHint($item, $table, $fields, $options);

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
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityFilterHint($item, string $table, array $fields, stdClass $options = null)
    {
        $options = $options ?? new stdClass();
        $options = parent::entityFilterHint($item, $table, $fields, $options);

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
}