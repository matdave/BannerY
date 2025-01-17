<?php

namespace BannerRevised\Model;

use MODX\Revolution\Sources\modMediaSource;
use xPDO\Om\xPDOSimpleObject;
use xPDO\xPDO;

/**
 * Class Ad
 *
 * @property string $name
 * @property string $url
 * @property string $image
 * @property integer $source
 * @property integer $active
 * @property string $description
 * @property string $start
 * @property string $end
 *
 * @property \BannerRevised\Model\AdPosition[] $Positions
 * @property \BannerRevised\Model\Click[] $Clicks
 *
 * @package BannerRevised\Model
 */
class Ad extends xPDOSimpleObject
{
    public function getImageUrl($image = '')
    {
        if (empty($image)) {
            $image = parent::get('image');
        };

        if (!empty($image) && $source = parent::get('source')) {
            /**
             * @var modMediaSource $source
             */
            if ($source = $this->xpdo->getObject(modMediaSource::class, $source)) {
                $source->initialize();
                //$image = $source->getObjectUrl($image);
                $bases = $source->getBases($image);
                $image = $bases['url'] . $image;
            }
        }

        return $image;
    }

    public function process()
    {
        if ($this->get('type') === 'html') {
            $html = $this->parseString($this->get('html'));
            $this->set('html', $html);
        }
        if ($this->get('type') === 'image') {
            $description = $this->parseString($this->get('description'));
            $this->set('description', $description);
            $image = $this->getImageUrl();
            $this->set('image', $image);
        }
        $url = $this->parseString($this->get('url'));
        $this->set('url', $url);
        return $this->toArray();
    }

    private function parseString($string)
    {
        $maxIterations = 10;
        $this->xpdo->elementCache = [];
        $this->xpdo->parser->processElementTags('', $string, false, false, '[[', ']]', [], $maxIterations);
        $this->xpdo->parser->processElementTags('', $string, true, false, '[[', ']]', [], $maxIterations);
        $this->xpdo->parser->processElementTags('', $string, true, true, '[[', ']]', [], $maxIterations);
        return $string;
    }
}
