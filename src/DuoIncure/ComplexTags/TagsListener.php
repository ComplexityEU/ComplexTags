<?php

namespace DuoIncure\ComplexTags;

use DuoIncure\ComplexTags\tasks\DisplayTagTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class TagsListener implements Listener {

	/** @var TagsMain */
	private $plugin;

	/**
	 * TagsListener constructor.
	 * @param TagsMain $plugin
	 */
	public function __construct(TagsMain $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		$playerName = $player->getName();
		$uuidBinary = $player->getRawUniqueId();
		if(!$this->plugin->getTagsDB()->userExists($uuidBinary)){
			$this->plugin->getTagsDB()->createNewUser($uuidBinary, $playerName, "testTag1:testTag2", "testTag1");
		}
		$this->plugin->getScheduler()->scheduleRepeatingTask(new DisplayTagTask($this->plugin), 10);
	}
}