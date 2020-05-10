<?php

namespace DuoIncure\ComplexTags\ui;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use DuoIncure\ComplexTags\TagsMain;
use BreathTakinglyBinary\libDynamicForms\SimpleForm;
use function strtoupper;

class TagsForm extends SimpleForm {

	public const LABEL_PREFIX = "TAG_";

	/** @var TagsMain */
	private $plugin;
	private $cfg;

	/**
	 * TagsForm constructor.
	 * @param TagsMain $plugin
	 * @param Player $player
	 */
	public function __construct(TagsMain $plugin, Player $player)
	{
		$this->plugin = $plugin;
		$this->cfg = $plugin->getConfig()->getAll();
		$uuid = $player->getRawUniqueId();
		$playerTags = $plugin->getTagsDB()->getAvailableTags($uuid);
		$currentTag = $plugin->getTagsDB()->getCurrentTag($uuid);
		parent::__construct();
		$this->setTitle(TextFormat::colorize(($this->cfg["form-title"] ?? "&l&6ComplexTags")));
		foreach ($playerTags as $tag){
			$ucaTag = strtoupper($tag);
			if($tag === $currentTag){
				$this->addButton(TF::colorize($this->cfg["tags"][$tag]["name"]) . TF::EOL . TF::GREEN . "Selected", self::LABEL_PREFIX . $ucaTag);
			} else {
				$this->addButton(TF::colorize($this->cfg["tags"][$tag]["name"]) . TF::EOL . TF::DARK_GRAY . "Select", (self::LABEL_PREFIX . $ucaTag));
			}
		}
	}

	/**
	 * @param Player $player
	 * @param $data
	 */
	public function onResponse(Player $player, $data): void
	{
		$uuid = $player->getRawUniqueId();
		$playerTags = $this->plugin->getTagsDB()->getAvailableTags($uuid);
		foreach ($playerTags as $tag){
			$ucaTag = strtoupper($tag);
			switch($data){
				case (self::LABEL_PREFIX . $ucaTag):
					$this->plugin->getTagsDB()->setCurrentTag($uuid, $tag);
					$player->sendMessage(TF::GREEN . "You successfully selected the tag: " . TF::colorize($this->cfg["tags"][$tag]["name"]));
					break;
			}
		}
	}
}