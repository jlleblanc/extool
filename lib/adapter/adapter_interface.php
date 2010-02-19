<?php
namespace Extool\Adapter;

interface AdapterInterface {
	public function decorateRepresentation(Representation $representation);
}