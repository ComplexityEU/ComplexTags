<?php

namespace DuoIncure\ComplexTags\tasks;

use pocketmine\scheduler\Task;
use DuoIncure\ComplexTags\TagsMain;
use pocketmine\utils\TextFormat as TF;

class DisplayTagTask extends Task {

	/** @var TagsMain */
	private $plugin;

	public function __construct(TagsMain $plugin){
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
			$uuid = $player->getRawUniqueId();
			$playerCurrentTag = $this->plugin->getTagsDB()->getCurrentTag($uuid);
			$tagToSet = $this->plugin->getConfig()->getNested("tags.$playerCurrentTag.name");
			$player->setScoreTag(TF::colorize($tagToSet));
		}
	}
}