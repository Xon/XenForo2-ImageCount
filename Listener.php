<?php

namespace SV\ImageCount;

class Listener
{
	public static function userEntityStructure(\XF\Mvc\Entity\Manager $em, \XF\Mvc\Entity\Structure &$structure)
	{
		$structure->getters['message_max_images'] = true;
	}
}