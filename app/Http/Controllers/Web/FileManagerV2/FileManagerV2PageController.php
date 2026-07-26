<?php

namespace App\Http\Controllers\Web\FileManagerV2;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FileManagerV2PageController extends Controller
{
    public function __invoke(): View
    {
        return view('filemanager_v2.index');
    }
}
