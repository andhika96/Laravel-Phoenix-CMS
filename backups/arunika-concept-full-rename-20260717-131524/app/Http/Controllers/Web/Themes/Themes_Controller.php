<?php

namespace App\Http\Controllers\Web\Themes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Themes_Controller extends Controller
{
	public function index()
	{
		return view('arunika_themes.arunika_v1');
	}

	public function gemini()
	{
		return view('arunika_themes.arunika_v1_gemini_extends');
	}
}

?>
