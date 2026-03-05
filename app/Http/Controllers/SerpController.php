<?php

namespace App\Http\Controllers;

use App\Http\Requests\SerpSearchRequest;
use App\Services\Serp\GoogleOrganicRankService;
use Illuminate\View\View;

final class SerpController extends Controller
{
    public function __construct(private readonly GoogleOrganicRankService $rankService) {}

    public function index(): View
    {
        return view('serp.index');
    }

    public function search(SerpSearchRequest $request): View
    {
        $input = $request->validated();

        $result = $this->rankService->findRank(
            keyword: $input['keyword'],
            site: $input['site'],
            locationName: $input['location'],
            languageName: $input['language'],
        );

        return view('serp.index', [
            'input' => $input,
            'result' => $result,
        ]);
    }
}
