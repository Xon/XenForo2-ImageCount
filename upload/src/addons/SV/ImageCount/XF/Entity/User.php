<?php

namespace SV\ImageCount\XF\Entity;

use XF\Entity\Forum;

class User extends XFCP_User
{
    /**
     * @param Forum $forum
     * @return int|null
     */
    public function getForumMessageMaxImages(Forum $forum)
    {
        $permVal = (int)$this->hasNodePermission($forum->node_id, 'sv_MaxImageCount');

        if ($permVal < 0)
        {
            // unlimited
            return 0;
        }

        if ($permVal === 0)
        {
            // do not apply
            return null;
        }

        return $permVal;
    }

    /**
     * @return int|null
     */
    public function getConversationMessageMaxImages()
    {
        $permVal = (int)$this->hasPermission('conversation', 'sv_MaxImageCount');

        if ($permVal < 0)
        {
            // unlimited
            return 0;
        }

        if ($permVal === 0)
        {
            // do not apply
            return null;
        }

        return $permVal;
    }
}
