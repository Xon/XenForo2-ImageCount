<?php

namespace SV\ImageCount\XF\Entity;

use XF\Entity\Forum;
use XF\Mvc\Entity\Structure;

class User extends XFCP_User
{
    public function getForumMessageMaxImages(Forum $forum)
    {
        $permVal = $this->hasNodePermission($forum->node_id, 'sv_MaxImageCount');

        if ($permVal == -1)
        {
            return PHP_INT_MAX;
        }

        if (!$permVal)
        {
            return $this->app()->options()->messageMaxImages;
        }

        return $permVal;
    }

    public function getConversationMessageMaxImages()
    {
        $permVal = $this->hasPermission('conversation', 'sv_MaxImageCount');

        if ($permVal == -1)
        {
            return PHP_INT_MAX;
        }

        if (!$permVal)
        {
            return $this->app()->options()->messageMaxImages;
        }

        return $permVal;
    }

    /**
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);
        $structure->getters['message_max_images'] = true;

        return $structure;
    }
}
