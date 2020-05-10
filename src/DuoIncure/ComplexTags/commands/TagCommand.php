<?php

namespace DuoIncure\ComplexTags\commands;

use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use DuoIncure\ComplexTags\TagsMain;

class TagCommand extends PluginCommand {

	/** @var TagsMain */
	private $plugin;
	private $cfg;

	public function __construct(string $name, Plugin $owner) {
		$this->plugin = $owner;
		$this->cfg = $owner->getConfig();
		parent::__construct($name, $owner);
		$this->setPermission("complextags.command.tag");
		$this->setDescription("Create/Add/Remove Tags");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$sender->hasPermission($this->getPermission())) {
			$sender->sendMessage(TF::RED . "You do not have permission to use this command!");
			return;
		}
		if(!isset($args[0])){
			$sender->sendMessage(TF::RED . "You need to provide some arguments!" . TF::EOL . "Usage: /tag <create:give:remove>");
			return;
		}
		$validTags = $this->plugin->getTagsDB()->getAvailableConfigTagNames();
		switch ($args[0]){
			case "create":
				if(!isset($args[1])){
					$sender->sendMessage(TF::RED . "You need to provide some arguments!" . TF::EOL . "Usage: /tag create <tagName> <colouredTagName>!");
					return;
				} elseif(strpos($args[1], "&") !== false){
					$sender->sendMessage(TF::RED . "You must provide a valid tag name!" . TF::EOL . "This can NOT include colours!");
					return;
				} elseif(!isset($args[2])){
					$sender->sendMessage(TF::RED . "You need to provide a coloured tag name!" . TF::EOL . "Use \"&\" for colours!");
					return;
				}
				$tagName = $args[1];
				$colouredTagName = $args[2];
				$this->plugin->getTagsDB()->createNewTag($tagName, $colouredTagName);
				break;
			case "give":
				if(!isset($args[1])){
					$sender->sendMessage(TF::RED . "You need to provide some arguments!" . TF::EOL . "Usage: /tag give <playerName> <tagName>");
					return;
				} elseif(!$this->plugin->getServer()->getPlayer($args[1]) instanceof Player){
					$sender->sendMessage(TF::RED . "You need to provide a valid player name!");
					return;
				} elseif(!isset($args[2])){
					$sender->sendMessage("You need to provide a tag name!" . TF::EOL . "Valid tag names: $validTags");
					return;
				} elseif(!$this->plugin->getTagsDB()->isValidTag($args[2])){
					$sender->sendMessage(TF::RED . "You must provide a valid tag name!");
					return;
				}
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				$uuid = $player->getRawUniqueId();
				$tagName = $args[2];
				$this->plugin->getTagsDB()->addTag($uuid, $tagName);
				$player->sendMessage(TF::GREEN . "You received the tag: " . TF::colorize($this->cfg->getNested("tags.$tagName.name")) . TF::GREEN . " !");
				break;
			case "remove":
				if(!isset($args[1])){
					$sender->sendMessage(TF::RED . "You need to provide some arguments!" . TF::EOL . "Usage: /tag remove <playerName> <tagName>");
					return;
				} elseif(!$this->plugin->getServer()->getPlayer($args[1]) instanceof Player){
					$sender->sendMessage(TF::RED . "You need to provide a valid player!");
					return;
				} elseif(!isset($args[2])){
					$sender->sendMessage("You need to provide a tag name!" . TF::EOL . "Valid tag names: $validTags");
					return;
				} elseif(!$this->plugin->getTagsDB()->isValidTag($args[2])){
					$sender->sendMessage(TF::RED . "You must provide a valid tag name!");
					return;
				}
				$player = $this->plugin->getServer()->getPlayer($args[1]);
				$uuid = $player->getRawUniqueId();
				$tagName = $args[2];
				if(!$this->plugin->getTagsDB()->hasTagAvailable($uuid, $tagName)){
					$sender->sendMessage(TF::RED . "That player does not have the tag to be removed!");
					return;
				}
				$this->plugin->getTagsDB()->removeTag($uuid, $tagName);
				break;
		}
	}
}