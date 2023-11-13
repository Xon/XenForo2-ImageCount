<?php

namespace SV\ImageCount\XF\Entity;

use XF\Entity\Forum;
use XF\Entity\Post;

class User extends XFCP_User
{
    public function getForumMessageMaxImages(Forum $forum): ?int
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

    public function getForumMessageMinImages(Forum $forum, ?Post $post): ?int
    {
        $isFirstPost = $post !== null && $post->isFirstPost();

        $permVal = (int)$this->hasNodePermission($forum->node_id, $isFirstPost ? 'svMinImageCountFirstPost' : 'svMinImageCountReplies');
        if ($permVal <= 0)
        {
            // do not apply
            return null;
        }

        return $permVal;
    }

    public function getConversationMessageMaxImages(): ?int
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
