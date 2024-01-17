<?php /** @noinspection PhpUnusedParameterInspection */

namespace SV\ImageCount\BbCode\ProcessorAction;

use XF\BbCode\ProcessorAction\AnalyzerHooks;
use XF\BbCode\ProcessorAction\AnalyzerInterface;
use XF\BbCode\ProcessorAction\FiltererHooks;
use XF\BbCode\ProcessorAction\FiltererInterface;

class AnalyzeQuotedImgUsage implements AnalyzerInterface, FiltererInterface
{
    protected $quoteDepth          = 0;
    public    $imagesInQuotes      = 0;
    public    $attachmentsInQuotes = 0;

    public function addFiltererHooks(FiltererHooks $hooks): void
    {
        $hooks->addSetupHook('setup')
              ->addTagHook('quote', 'filterQuoteTag');
    }

    public function addAnalysisHooks(AnalyzerHooks $hooks): void
    {
        $hooks->addTagHook('quote', 'analyzeQuoteTag')
              ->addTagHook('img', 'analyzeImageTag')
              ->addTagHook('attach', 'analyzeAttachTag');
    }

    public function setup(): void
    {
        $this->quoteDepth = 0;
        $this->imagesInQuotes = 0;
        $this->attachmentsInQuotes = 0;
    }

    public function filterQuoteTag(array $tag, array $options)
    {
        $this->quoteDepth++;
    }

    public function analyzeQuoteTag(array $tag, array $options)
    {
        $this->quoteDepth--;
    }

    public function analyzeImageTag(array $tag, array $options)
    {
        if ($this->quoteDepth > 0)
        {
            $this->imagesInQuotes++;
        }
    }

    public function analyzeAttachTag(array $tag, array $options)
    {
        if ($this->quoteDepth > 0)
        {
            $this->attachmentsInQuotes++;
        }
    }
}