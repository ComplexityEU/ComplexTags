<?php

namespace DuoIncure\ComplexTags\commands;

use DuoIncure\ComplexTags\ui\TagsForm;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use DuoIncure\ComplexTags\TagsMain;

class TagsCommand extends PluginCommand {

	/** @var TagsMain */
	private $plugin;
	private $cfg;

	public function __construct(string $name, Plugin $owner) {
		$this->plugin = $owner;
		$this->cfg = $owner->getConfig();
		parent::__construct($name, $owner);
		$this->setPermission("complextags.command.tags");
		$this->setDescription("See/Choose your tags!");
		$this->setAliases(["complextags"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$sender instanceof Player) {
			$sender->sendMessage(TF::RED . "You need to be in-game to use this command!");
			return;
		}
		if(!$sender->hasPermission($this->getPermission())){
			$sender->sendMessage(TF::RED . "You do not have permission to use this command!");
			return;
		}
		$sender->sendForm(new TagsForm($this->plugin, $sender));
	}
}