<?php

namespace SV\ImageCount\Option;

use XF\Entity\ThreadPrefixGroup;
use XF\Option\AbstractOption;
use function array_map;

abstract class ThreadPrefix extends AbstractOption
{
    public static function renderOption(\XF\Entity\Option $option, array $htmlParams): string
    {
        $choices = [
            [
                'value' => 0,
                'label' => \XF::phrase('(none)'),
                '_type' => 'option'
            ]
        ];

        /** @var \XF\Repository\ThreadPrefix $prefixRepo */
        $prefixRepo = \XF::repository('XF:ThreadPrefix');
        $prefixListData = $prefixRepo->getPrefixListData();

        /** @var ThreadPrefixGroup $prefixGroup */
        foreach ($prefixListData['prefixGroups'] as $prefixGroup)
        {
            /** @var \XF\Entity\ThreadPrefix[] $prefixesByGroup */
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
    public static function verifyOption(&$value, \XF\Entity\Option $option): bool
    {
        assert(is_array($value));
        $value = array_map('\intval', $value);

        return true;
    }
}