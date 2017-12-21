<?php

namespace Smuuf\Primi;

interface IReadlineDriver {

	public function readline(string $prompt): string;
	public function readlineAddHistory(string $item): void;
	public function readlineReadHistory(string $path): void;
	public function readlineWriteHistory(string $path): void;

}
