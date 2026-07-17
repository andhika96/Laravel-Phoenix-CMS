<?php

namespace App\Http\Controllers\Web\Themes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Themes_Controller extends Controller
{
	public function index()
	{
		return view('arunika_themes.arunika_mosaic');
	}

	public function gemini()
	{
		return view('arunika_themes.arunika_mosaic_gemini_extends');
	}
}

?>
