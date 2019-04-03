<?php
namespace packages\userpanel\user;
use packages\base\db\dbObject;
use packages\userpanel\{App, user, date};

class Api extends dbObject {
	const active = 1;
	const disable = 2;
	protected $dbTable = "userpanel_users_apis";
	protected $primaryKey = "id";
	protected $dbFields = array(
		"user" => array("type" => "int", "required" => true),
		"app" => array("type" => "int", "required" => true),
		"token" => array("type" => "text", "required" => true),
		"create_at" => array("type" => "int", "required" => true),
		"status" => array("type" => "int", "required" => true),
	);
	protected $relations = array(
		"user" => array("hasOne", user::class, "user"),
		"app" => array("hasOne", App::class, "app")
	);
	public function preLoad(array $data): array {
		if (!isset($data["create_at"])) {
			$data["create_at"] = date::time();
		}
		if (!isset($data["status"])) {
			$data["status"] = Self::active;
		}
		return $data;
	}
}
