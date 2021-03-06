<?php

namespace SV\ImageCount\XF\Entity;

use XF\Entity\Forum;

class User extends XFCP_User
{
    /**
     * @param Forum $forum
     * @return bool|int
     */
    public function getForumMessageMaxImages(Forum $forum)
    {
        $permVal = $this->hasNodePermission($forum->node_id, 'sv_MaxImageCount');

        if ($permVal)
        {
            if ($permVal < 0)
            {
                $permVal = 0;
            }

            return $permVal;
        }

        return false;
    }

    /**
     * @return bool|int
     */
    public function getConversationMessageMaxImages()
    {
        $permVal = $this->hasPermission('conversation', 'sv_MaxImageCount');

        if ($permVal)
        {
            if ($permVal < 0)
            {
                $permVal = 0;
            }

            return $permVal;
        }

        return false;
    }
}
