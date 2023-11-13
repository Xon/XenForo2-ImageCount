<?php

namespace SV\ImageCount\XF\Service\Message;



use XF\Phrase;

/**
 * @extends \XF\Service\Message\Preparer
 */
class Preparer extends XFCP_Preparer
{
    protected $svImageTags = [
        'img',
        'attach',
        'bimg', // SV/AdvancedBbCodesPack
    ];

    protected function setup()
    {
        parent::setup();

        $this->setConstraint('minImages', 0);
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function checkValidity($message)
    {
        $isValid = parent::checkValidity($message);

        $error = $this->checkMinImages();
        if ($error !== null)
        {
            $this->errors[] = $error;
        }

        return $isValid;
    }

    public function checkMinImages(): ?\XF\Phrase
    {
        $error = null;
        /** @var \XF\BbCode\ProcessorAction\AnalyzeUsage $usage */
        $usage = $this->bbCodeProcessor->getAnalyzer('usage');

        $minImages = (int)($this->constraints['minImages'] ?? 0);
        if ($minImages > 0)
        {
            $hasValidTag = false;
            foreach ($this->svImageTags as $tag)
            {
                if ($usage->getTagCount($tag) >= $minImages)
                {
                    $hasValidTag = true;
                    break;
                }
            }

            if (!$hasValidTag)
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