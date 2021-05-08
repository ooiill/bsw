<?php

namespace App\Module\Form;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Upload;

class ImageUpload extends Upload
{
    /**
     * @var int
     */
    protected int $maxWidth = 200;

    /**
     * @var int
     */
    protected int $maxHeight = 200;

    /**
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * @param int $maxWidth
     *
     * @return $this
     */
    public function setMaxWidth(int $maxWidth)
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @param int $maxHeight
     *
     * @return $this
     */
    public function setMaxHeight(int $maxHeight)
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyForInit(): string
    {
        return Helper::camelToUnder($this->key, '-');
    }
}
