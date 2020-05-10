<?php

namespace DuoIncure\ComplexTags\sqlite;

use SQLite3;
use DuoIncure\ComplexTags\TagsMain;
use function array_push;
use function explode;
use function implode;
use function in_array;

class TagsDB{

	/** @var TagsMain */
	private $plugin;
	/** @var SQLite3 */
	private $db;

	public function __construct(TagsMain $plugin){
		$this->plugin = $plugin;
		$this->db = new SQLite3($this->plugin->getDataFolder() . "tagsDB.db", SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);

		$this->db->exec("CREATE TABLE IF NOT EXISTS complexTags(
					id INTEGER PRIMARY KEY NOT NULL,
					uuid BLOB NOT NULL,
					playerName VARCHAR(16) NOT NULL,
					availableTags TEXT NOT NULL,
					currentTag VARCHAR(32) NOT NULL
				)
			");
	}

	/**
	 * @param string $uuid
	 * @return bool
	 */
	public function userExists(string $uuid){
		return ($this->getUUIDByUUID($uuid) != null);
	}

	public function createNewUser(string $uuid, string $name, string $availableTags, string $currentTag){
		$query = "INSERT INTO complexTags(uuid, playerName, availableTags, currentTag) VALUES(:uuid, :playerName, :availableTags, :currentTag)";
		$q = $this->db->prepare($query);
		$q->bindValue(":uuid", $uuid);
		$q->bindValue(":playerName", $name);
		$q->bindValue(":availableTags", $availableTags);
		$q->bindValue(":currentTag", $currentTag);
		$q->execute();
	}

	/**
	 * @param string $uuid
	 * @return mixed
	 */
	public function getCurrentTag(string $uuid){
		$query = "SELECT currentTag FROM complexTags WHERE uuid = :uuid";
		return $this->prep(
			$query, "currentTag", "uuid", $uuid
		);
	}

	/**
	 * @param string $uuid
	 * @param string $tagToSet
	 */
	public function setCurrentTag(string $uuid, string $tagToSet){
		$query = "UPDATE complexTags SET currentTag = :currentTag WHERE uuid = :uuid";
		$q = $this->db->prepare($query);
		$q->bindValue(":currentTag", $tagToSet);
		$q->bindValue(":uuid", $uuid);
		$q->execute();
	}

	/**
	 * @param string $uuid
	 * @return array
	 */
	public function getAvailableTags(string $uuid){
		$query = "SELECT availableTags FROM complexTags WHERE uuid = :uuid";
		return explode(":", $this->prep($query, "availableTags", "uuid", $uuid));
	}

	/**
	 * @param string $uuid
	 * @param string $tagToAdd
	 */
	public function addTag(string $uuid, string $tagToAdd){
		$tags = $this->getAvailableTags($uuid);
		$tags[] = $tagToAdd;
		$toSet = implode(":", $tags);
		$query = "UPDATE complexTags SET availableTags = :availableTags WHERE uuid = :uuid";
		$q = $this->db->prepare($query);
		$q->bindValue(":availableTags", $toSet);
		$q->bindValue(":uuid", $uuid);
		$q->execute();
	}

	/**
	 * @param string $uuid
	 * @param string $tagToRemove
	 */
	public function removeTag(string $uuid, string $tagToRemove){
		$tags = $this->getAvailableTags($uuid);
		$index = array_search($tagToRemove, $tags);
		unset($tags[$index]);
		$toSet = implode(":", $tags);
		$query = "UPDATE complexTags SET availableTags = :availableTags WHERE uuid = :uuid";
		$q = $this->db->prepare($query);
		$q->bindValue(":availableTags", $toSet);
		$q->bindValue(":uuid", $uuid);
		$q->execute();
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getUUIDByName(string $name){
		$query = "SELECT uuid FROM complexTags WHERE playerName = :playerName";
		return $this->prep(
			$query, "uuid", "playerName", $name
		);
	}

	/**
	 * @param string $uuid
	 * @return mixed
	 */
	public function getNameByUUID(string $uuid){
		$query = "SELECT playerName FROM complexTags WHERE uuid = :uuid";
		return $this->prep(
			$query, "playerName", "uuid", $uuid
		);
	}

	public function getUUIDByUUID(string $uuid){
		$query = "SELECT uuid FROM complexTags WHERE uuid = :uuid";
		return $this->prep(
			$query, "uuid", "uuid", $uuid
		);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getNameByName(string $name){
		$query = "SELECT playerName FROM complexTags WHERE playerName = :playerName";
		return $this->prep(
			$query, "playerName", "playerName", $name
		);
	}

	/**
	 * @param string $query
	 * @param string $select
	 * @param string $where
	 * @param string $value
	 * @return mixed
	 */
	public function prep($query, string $select, string $where, $value)
	{
		$q = $this->db->prepare($query);
		$q->bindValue(":{$where}", $value);
		$result = $q->execute();
		return $result->fetchArray(SQLITE3_ASSOC)[$select];
	}

	/**
	 * @param string $uuid
	 * @param string $tagToCheck
	 * @return bool
	 */
	public function hasTagAvailable(string $uuid, string $tagToCheck){
		$tags = $this->getAvailableTags($uuid);
		$return = false;
		if(in_array($tagToCheck, $tags, true)){
			$return = true;
		}
		return $return;
	}

	/**
	 * @param string $name
	 * @param string $colouredName
	 */
	public function createNewTag(string $name, string $colouredName){
		$config = $this->plugin->getConfig();
		$config->setNested("tags.$name.name", '' . $colouredName . '');
		$config->save();
	}

	/**
	 * @return string
	 */
	public function getAvailableConfigTagNames(){
		$returnArray = [];
		foreach($this->plugin->getConfig()->get("tags") as $name => $data){
			array_push($returnArray, $name);
		}
		return implode(", ", $returnArray);
	}

	/**
	 * @param string $tagToCheck
	 * @return bool
	 */
	public function isValidTag(string $tagToCheck){
		$tagArray = [];
		$valid = false;
		foreach ($this->plugin->getConfig()->get("tags") as $tag => $data){
			array_push($tagArray, $tag);
		}
		if(in_array($tagToCheck, $tagArray)) {
			$valid = true;
		}
		return $valid;
	}
}