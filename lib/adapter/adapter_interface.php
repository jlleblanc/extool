<?php
namespace Extool\Adapter;

interface AdapterInterface {
	public function decorateRepresentation(Representation $representation);
	public function setResource($resource);
	public function parse();
}