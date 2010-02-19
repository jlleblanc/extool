<?php
namespace Extool\Adapter;

interface AdapterInterface {
	public function getTables();
	public function getPublicViews();
	public function getAdminViews();
	public function getData();
	public function getName();
	public function getPublicModels();
	public function getAdminModels();
}