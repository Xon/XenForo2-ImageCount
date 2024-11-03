<?php

namespace SV\ImageCount\XF\Entity;

use XF\Entity\Forum as ForumEntity;
use XF\Entity\Post as PostEntity;
use function array_intersect;
use function count;

class User extends XFCP_User
{
    public function getForumMessageMaxImages(ForumEntity $forum): ?int
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

    public function getForumMessageMinImages(ForumEntity $forum, PostEntity $post): ?int
    {
        $isFirstPost = $post->isFirstPost();

        $permVal = (int)$this->hasNodePermission($forum->node_id, $isFirstPost ? 'svMinImageCountFirstPost' : 'svMinImageCountReplies');
        if ($permVal <= 0)
        {
            // do not apply
            return null;
        }

        $prefixes = \XF::options()->svMinImagePrefixes ?? [];
        if (count($prefixes) === 0)
        {
            return $permVal;
        }

        /** @var \SV\MultiPrefix\XF\Entity\Thread $thread */
        $thread = $post->Thread;
        $prefixId = $thread->prefix_id;
        $threadPrefixes = [];
        if ($prefixId !== 0)
        {
            if (\XF::isAddOnActive('SV/MultiPrefix'))
            {
                $threadPrefixes = $thread->sv_prefix_ids ?? [$prefixId];
            }
            else
            {
                $threadPrefixes = [$prefixId];
            }
        }

        if (count($threadPrefixes) === 0)
        {
            return null;
        }
        else if (count(array_intersect($prefixes, $threadPrefixes)) === 0)
        {
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
