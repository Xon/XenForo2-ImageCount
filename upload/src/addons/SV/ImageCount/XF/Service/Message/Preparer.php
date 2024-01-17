<?php

namespace SV\ImageCount\XF\Service\Message;

use SV\ImageCount\BbCode\ProcessorAction\AnalyzeQuotedImgUsage;
use function max;

/**
 * @extends \XF\Service\Message\Preparer
 */
class Preparer extends XFCP_Preparer
{
    public $svAttachCount = 0;

    protected $svImageTags = [
        'img',
        'attach',
        //'bimg', // SV/AdvancedBbCodesPack shims img count to include bimg
    ];

    protected function setup()
    {
        parent::setup();

        $this->setConstraint('minImages', 0);
        $this->filters['svExtractQuotedImgStats'] = (bool)(\XF::options()->svExcludeQuotedImagesFromCount ?? false);
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function checkValidity($message)
    {
        $undoPatchCount = null;
        /** @var ?AnalyzeQuotedImgUsage $quotedUsage */
        $quotedUsage = $this->bbCodeProcessor->getAnalyzer(AnalyzeQuotedImgUsage::class);
        if ($quotedUsage !== null && $quotedUsage->imagesInQuotes !== 0)
        {
            /** @var ?\XF\BbCode\ProcessorAction\AnalyzeUsage $usage */
            $usage = $this->bbCodeProcessor->getAnalyzer('usage');
            if ($usage !== null)
            {
                $originalImageCount = 0;
                $patchImgCount = \Closure::bind(function () use ($quotedUsage, & $originalImageCount) {
                    $originalImageCount = $this->tagCount['img'] ?? 0;
                    $this->tagCount['img'] = max(0, $originalImageCount - $quotedUsage->imagesInQuotes);
                }, $usage, $usage);
                $undoPatchCount = \Closure::bind(function () use ($quotedUsage, $originalImageCount) {
                    $this->tagCount['img'] = $originalImageCount;
                }, $usage, $usage);

                $patchImgCount();
            }
        }
        try
        {
            $isValid = parent::checkValidity($message);
        }
        finally
        {
            if ($undoPatchCount !== null)
            {
                $undoPatchCount();
            }
        }

        $error = $this->checkMinImages();
        if ($error !== null)
        {
            $this->errors[] = $error;
        }

        return $isValid;
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    protected function getBbCodeProcessor()
    {
        $processor = parent::getBbCodeProcessor();

        if ($this->filters['svExtractQuotedImgStats'])
        {
            $bbCodeContainer = $this->app->bbCode();
            $processor->addProcessorAction(AnalyzeQuotedImgUsage::class, $bbCodeContainer->processorAction(AnalyzeQuotedImgUsage::class));
        }

        return $processor;
    }

    public function svSetupAttachmentCount(string $contentType, ?int $contentId, ?string $tempAttachmentHash): void
    {
        $count = 0;
        if ($contentId !== null)
        {
            $count += (int)\XF::db()->fetchOne('
                SELECT COUNT(*)
                FROM xf_attachment AS attach
                JOIN xf_attachment_data AS attachData ON attach.data_id = attachData.data_id
                WHERE attach.content_type = ? AND attach.content_id = ? AND attachData.width > 0
            ', [$contentType, $contentId]);
        }

        $tempAttachmentHash = (string)$tempAttachmentHash;
        if ($tempAttachmentHash !== '')
        {
            $count += (int)\XF::db()->fetchOne('
                SELECT COUNT(*)
                FROM xf_attachment as attach
                JOIN xf_attachment_data as attachData on attach.data_id = attachData.data_id
                WHERE attach.temp_hash = ? AND attachData.width > 0
            ', [$tempAttachmentHash]);
        }

        $this->svAttachCount = $count;
    }

    public function checkMinImages(): ?\XF\Phrase
    {
        $error = null;
        /** @var \XF\BbCode\ProcessorAction\AnalyzeUsage $usage */
        $usage = $this->bbCodeProcessor->getAnalyzer('usage');

        $minImages = (int)($this->constraints['minImages'] ?? 0);
        if ($minImages > 0)
        {
            $imageCount = $this->svAttachCount;
            foreach ($this->svImageTags as $tag)
            {
                $imageCount += (int)$usage->getTagCount($tag);
            }

            /** @var AnalyzeQuotedImgUsage $quotedUsage */
            $quotedUsage = $this->bbCodeProcessor->getAnalyzer(AnalyzeQuotedImgUsage::class);
            if ($quotedUsage !== null)
            {
                if ($this->svAttachCount > 0)
                {
                    $imageCount -= $quotedUsage->attachmentsInQuotes;
                }
                $imageCount = $imageCount - $quotedUsage->imagesInQuotes;
                $imageCount = max(0, $imageCount);
            }

            if ($imageCount < $minImages)
            {
                $error = \XF::phraseDeferred(
                    $minImages === 1
                        ? 'sv_please_enter_message_with_at_least_1_image'
                        : 'sv_please_enter_message_with_at_least_x_images',
                    ['count' => $minImages]
                );
            }
        }

        return $error;
    }
}