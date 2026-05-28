<?php

namespace App\Support;

use Symfony\Component\Console\Output\StreamOutput;

class SseStreamOutput extends StreamOutput
{
	public function __construct()
	{
		// Wajib: parent butuh resource stream valid
		$stream = fopen('php://output', 'w');
		
		parent::__construct($stream);
	}

	// Signature HARUS sama persis dengan parent:
	protected function doWrite(string $message, bool $newline): void
	{
		$line = rtrim($message, "\r\n");
		
		if ($line !== '') 
		{
			// echo "data: {$line}\n"; // kirim sebagai event SSE default
			echo "{$line}\n\n";
			
			if (function_exists('ob_flush')) 
			{
				@ob_flush();
			}

			flush();
		}

		// Jangan panggil parent::doWrite() supaya tidak dobel output
	}
}
