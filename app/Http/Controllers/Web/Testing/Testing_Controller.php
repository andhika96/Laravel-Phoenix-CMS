<?php

namespace App\Http\Controllers\Web\Testing;

use App\Http\Controllers\Controller;

use App\Models\Oil_Palm_Tree_v3;

use Illuminate\Http\Request;

class Testing_Controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return view('testing.testing');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function add()
	{
		return view('testing.testing_add');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 */
	public function show()
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit()
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy()
	{
		//
	}

	public function test()
	{
		
	}

	public function benchmark()
	{
		// Mengambil 50 data dengan id acak agar cache database tidak curang
		// Pastikan kolom 'id' ter-index (biasanya default sudah index)
		$result = Oil_Palm_Tree_v3::select('id', 'name')->where('id', '>', rand(1, 2000000))->limit(50)->get();

		return response()->json($result);
	}
}
