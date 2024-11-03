<?php

namespace SV\ImageCount\Option;

use SV\StandardLib\Helper;
use XF\Entity\Option as OptionEntity;
use XF\Entity\ThreadPrefix as ThreadPrefixEntity;
use XF\Entity\ThreadPrefixGroup as ThreadPrefixGroupEntity;
use XF\Option\AbstractOption;
use XF\Repository\ThreadPrefix as ThreadPrefixRepo;
use function array_map;
use function assert;
use function is_array;

abstract class ThreadPrefix extends AbstractOption
{
    public static function renderOption(OptionEntity $option, array $htmlParams): string
    {
        $choices = [
            [
                'value' => 0,
                'label' => \XF::phrase('(none)'),
                '_type' => 'option'
            ]
        ];

        $prefixRepo = Helper::repository(ThreadPrefixRepo::class);
        $prefixListData = $prefixRepo->getPrefixListData();

        /** @var ThreadPrefixGroupEntity $prefixGroup */
        foreach ($prefixListData['prefixGroups'] as $prefixGroup)
        {
            /** @var ThreadPrefixEntity[] $prefixesByGroup */
            $prefixesByGroup = $prefixListData['prefixesGrouped'][$prefixGroup->prefix_group_id] ?? null;
            if ($prefixesByGroup === null)
            {
                continue;
            }

            $options = [];
            foreach ($prefixesByGroup as $prefix)
            {
                $options[] = [
                    'value' => $prefix->prefix_id,
                    'label' => \XF::escapeString($prefix->title),
                    '_type' => 'option'
                ];
            }
            $choices[] = [
                'label'   => \XF::escapeString($prefixGroup->title),
                '_type'   => 'optgroup',
                'options' => $options,
            ];
        }


        $controlOptions = self::getControlOptions($option, $htmlParams);
        $controlOptions['multiple'] = true;
        $rowOptions = self::getRowOptions($option, $htmlParams);

        return self::getTemplater()->formSelectRow($controlOptions, $choices, $rowOptions);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public static function verifyOption(&$value, OptionEntity $option): bool
    {
        assert(is_array($value));
        $value = array_map('\intval', $value);

        return true;
    }
}