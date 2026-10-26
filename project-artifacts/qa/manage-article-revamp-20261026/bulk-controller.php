	public function bulk_update(Request $request)
	{
        $input = $request->validate([
            'mode' => ['required', 'in:bulk_update'],
            'changeStatus' => ['required', 'string', 'max:64'],
            'getSelected' => ['required', 'array', 'min:1', 'max:50'],
            'getSelected.*' => ['required', 'integer', 'distinct', 'exists:articles,id'],
        ]);
        $status = Article_Status::where('is_active', 0)->where(function ($query) use ($input) {
            $query->where('code_name', $input['changeStatus']);
            if (ctype_digit($input['changeStatus'])) $query->orWhere('id', (int) $input['changeStatus']);
        })->first();
        if (!$status) {
            throw \Illuminate\Validation\ValidationException::withMessages(['changeStatus' => t('Status artikel tidak valid.')]);
        }
        DB::transaction(function () use ($input, $status) {
            $articles = Article::whereIn('id', $input['getSelected'])->lockForUpdate()->get();
            if ($articles->count() !== count($input['getSelected'])) {
                throw \Illuminate\Validation\ValidationException::withMessages(['getSelected' => t('Pilihan artikel sudah berubah. Muat ulang daftar.')]);
            }
            foreach ($articles as $article) {
                $article->fill(['status' => $status->code_name])->saveOrFail();
            }
        });
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'success', 'message' => t('Article successfully updated')]);
        }
        return redirect()->back()->with('success', t('Article successfully updated'));
	}

