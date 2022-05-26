<?php

declare(strict_types=1);

namespace Smuuf\Primi\Drivers;

interface ReplIoDriverInterface extends StdIoDriverInterface {

	public function addToHistory(string $item): void;
	public function loadHistory(string $path): void;
	public function storeHistory(string $path): void;

}
