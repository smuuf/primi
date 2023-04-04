<?php

declare(strict_types=1);

namespace Smuuf\Primi\Drivers;

interface StdIoDriverInterface {

	public function input(string $prompt): string;
	public function stdout(string ...$text): void;
	public function stderr(string ...$text): void;

}
