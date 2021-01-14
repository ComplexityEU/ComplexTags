<?php
declare(strict_types=1);

namespace DuoIncure\ComplexTags;

use DuoIncure\ComplexTags\commands\TagCommand;
use DuoIncure\ComplexTags\commands\TagsCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use DuoIncure\ComplexTags\sqlite\TagsDB;
use RuntimeException;
use function version_compare;
use function file_exists;
use function mkdir;

class TagsMain extends PluginBase {

	public const VERSION = "1";

	/** @var Config */
	private $cfg;
	/** @var TagsDB */
	private $tagsDB;

	public function onEnable()
	{
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		} elseif(!file_exists($this->getDataFolder() . "config.yml")){
			$this->saveDefaultConfig();
			$this->getLogger()->info("Config not found! Creating new config...");
		}
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$this->cfg = $this->cfg->getAll();
		if(version_compare(self::VERSION, $this->cfg["version"], ">")){
			$this->getLogger()->error("Your config file is outdated! Please delete your current config file and restart the server...");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
		$this->tagsDB = new TagsDB($this);
		$this->getServer()->getPluginManager()->registerEvents(new TagsListener($this), $this);
		$this->getServer()->getCommandMap()->registerAll("complextags", [
			new TagsCommand("tags", $this),
			new TagCommand("tag", $this)
		]);
	}

	public function getTagsDB(){
		if(!$this->tagsDB instanceof TagsDB){
			$this->getLogger()->error("tagsDB was not an instance of TagsDB");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
		return $this->tagsDB;
	}
}
