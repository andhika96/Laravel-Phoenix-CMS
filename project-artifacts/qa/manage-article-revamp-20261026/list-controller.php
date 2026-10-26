	public function listData(Request $request)
	{
        $input = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'filter_by_status' => ['nullable', 'string', 'max:64'],
            'filter_by_category' => ['nullable', 'integer', 'exists:article_categories,id'],
            'filter_by_scheduled' => ['nullable', 'in:true,false'],
            'sort' => ['nullable', 'in:newest,oldest,title_asc,title_desc'],
            'per_page' => ['nullable', 'integer', 'in:5,10,15,25,50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $query = Article::query();
        if (filled($input['search'] ?? null)) $query->whereLike('title', '%'.$input['search'].'%');
        if (filled($input['filter_by_category'] ?? null)) $query->where('category_id', $input['filter_by_category']);
        if (filled($input['filter_by_scheduled'] ?? null)) $query->where('scheduled', $input['filter_by_scheduled']);

        $statuses = Article_Status::where('is_active', 0)->get();
        $totals = (clone $query)->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $counts = ['all' => (int) $totals->sum()];
        foreach ($statuses as $status) {
            $counts[$status->code_name] = (int) $totals->get($status->code_name, 0) + (int) $totals->get((string) $status->id, 0);
        }
        if (filled($input['filter_by_status'] ?? null)) {
            $status = $statuses->firstWhere('code_name', $input['filter_by_status']);
            $query->whereIn('status', $status ? [$status->code_name, (string) $status->id] : [$input['filter_by_status']]);
        }
        [$column, $direction] = match ($input['sort'] ?? 'newest') {
            'oldest' => ['created_at', 'asc'],
            'title_asc' => ['title', 'asc'],
            'title_desc' => ['title', 'desc'],
            default => ['created_at', 'desc'],
        };
        $result = $query->with(['getStatus', 'category', 'author'])->orderBy($column, $direction)->orderBy('id', $direction)->paginate($input['per_page'] ?? 15);
        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($result, ManageArticleListResource::class);
        $formatted['status_counts'] = $counts;
        return $api->setStatusMsg($formatted['total'] ? 'success' : 'failed')->respondOK($formatted, $formatted['total'] ? 'Data found' : 'No data found');
	}

