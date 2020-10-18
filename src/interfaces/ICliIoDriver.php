<?php

namespace Smuuf\Primi;

interface ICliIoDriver {

	public function input(string $prompt): string;
	public function output(string $text): void;
	public function addToHistory(string $item): void;
	public function loadHistory(string $path): void;
	public function storeHistory(string $path): void;

}
