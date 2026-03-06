<?php

namespace App\Http\Controllers;

use App\Http\Requests\SerpSearchRequest;
use App\Services\Serp\GoogleOrganicRankResult;
use App\Services\Serp\GoogleOrganicRankService;
use App\Support\SerpLanguages;
use App\Support\SerpLocations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SerpController extends Controller
{
    public function __construct(private readonly GoogleOrganicRankService $rankService) {}

    public function index(): View
    {
        $result = session('result');

        return view('serp.index', [
            'input' => session()->getOldInput(),
            'result' => $result instanceof GoogleOrganicRankResult ? $result : null,
        ] + $this->viewData());
    }

    public function search(SerpSearchRequest $request): RedirectResponse
    {
        $input = $request->validated();
        $input['location_label'] = SerpLocations::labelByCode((int) $input['location_code']);

        $result = $this->rankService->findRank(
            keyword: $input['keyword'],
            site: $input['site'],
            locationCode: (int) $input['location_code'],
            languageCode: (string) $input['language_code'],
        );

        return redirect()
            ->route('serp.index')
            ->withInput($input)
            ->with('result', $result);
    }

    public function locations(Request $request): JsonResponse
    {
        $q = (string) $request->query('q', '');
        $limit = (int) $request->query('limit', 5);

        $data = SerpLocations::suggest($q, min(5, max(1, $limit)));

        return response()->json(['data' => $data]);
    }

    /**
     * @return array<string, mixed>
     */
    private function viewData(): array
    {
        $defaultLocationCode = (int) config('serp.defaults.location_code', 2804);
        $defaultLanguageCode = (string) config('serp.defaults.language_code', 'uk');

        return [
            'languages' => SerpLanguages::all(),
            'defaults' => [
                'location_code' => $defaultLocationCode,
                'location_label' => SerpLocations::labelByCode($defaultLocationCode),
                'language_code' => $defaultLanguageCode,
            ],
        ];
    }
}
