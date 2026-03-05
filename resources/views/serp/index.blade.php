<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ __('serp.page_title') }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        crossorigin="anonymous"
    >

    <style>
        body {
            background: #f6f7fb;
        }

        .container {
            max-width: 860px;
        }
    </style>
</head>
<body class="py-5">
<div class="container">
    <header class="mb-4">
        <h1 class="h3 mb-2">{{ __('serp.heading') }}</h1>
        <p class="text-secondary mb-0">{{ __('serp.lead') }}</p>
    </header>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="POST" action="{{ route('serp.search') }}" id="serpForm" class="vstack gap-3">
                @csrf

                <div>
                    <label for="keyword" class="form-label">{{ __('serp.form.keyword.label') }}</label>
                    <input
                        id="keyword"
                        name="keyword"
                        class="form-control"
                        value="{{ old('keyword', $input['keyword'] ?? '') }}"
                        placeholder="{{ __('serp.form.keyword.placeholder') }}"
                        required
                        autofocus
                    >
                    @error('keyword')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="site" class="form-label">{{ __('serp.form.site.label') }}</label>
                    <input
                        id="site"
                        name="site"
                        class="form-control"
                        value="{{ old('site', $input['site'] ?? '') }}"
                        placeholder="{{ __('serp.form.site.placeholder') }}"
                        required
                    >
                    @error('site')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text">{{ __('serp.form.site.hint') }}</div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="location_label" class="form-label">{{ __('serp.form.location.label') }}</label>
                        <input
                            id="location_label"
                            name="location_label"
                            list="locationOptions"
                            class="form-control"
                            value="{{ old('location_label', $input['location_label'] ?? ($defaults['location_label'] ?? '')) }}"
                            placeholder="{{ __('serp.form.location.placeholder') }}"
                            required
                        >
                        <datalist id="locationOptions"></datalist>
                        <input
                            id="location_code"
                            name="location_code"
                            type="hidden"
                            value="{{ old('location_code', $input['location_code'] ?? ($defaults['location_code'] ?? '')) }}"
                        >
                        @error('location_code')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('serp.form.location.hint') }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="language_code" class="form-label">{{ __('serp.form.language.label') }}</label>
                        <select id="language_code" name="language_code" class="form-select" required>
                            @php($selectedLanguage = old('language_code', $input['language_code'] ?? ($defaults['language_code'] ?? '')))
                            @foreach(($languages ?? []) as $lang)
                                @php($code = is_array($lang) ? ($lang['value'] ?? '') : '')
                                @php($label = is_array($lang) ? ($lang['label'] ?? '') : '')
                                @if(is_string($code) && $code !== '' && is_string($label) && $label !== '')
                                    <option value="{{ $code }}" @selected($selectedLanguage === $code)>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('language_code')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button id="submitBtn" type="submit" class="btn btn-primary">{{ __('serp.form.submit') }}</button>
                <div id="progress" class="form-text" aria-live="polite"></div>
            </form>
        </div>
    </div>

    @isset($result)
        <div class="card shadow-sm">
            <div class="card-body">
                @if($result->isFound())
                    <span class="badge text-bg-success">{{ __('serp.result.status.found') }}</span>

                    <div class="mt-3 vstack gap-1">
                        <div class="fs-5">
                            <strong>{{ __('serp.result.labels.organic_position') }}</strong> #{{ $result->rankGroup }}
                        </div>
                        <div class="text-secondary">
                            <strong>{{ __('serp.result.labels.domain') }}</strong> {{ $result->targetDomain }}
                        </div>

                        @if($result->rankAbsolute)
                            <div class="text-secondary">
                                <strong>{{ __('serp.result.labels.absolute_position') }}</strong> #{{ $result->rankAbsolute }}
                            </div>
                        @endif

                        @if($result->url)
                            <div class="text-secondary">
                                <strong>{{ __('serp.result.labels.url') }}</strong>
                                <a href="{{ $result->url }}" target="_blank" rel="noopener">{{ $result->url }}</a>
                            </div>
                        @endif

                        @if($result->title)
                            <div class="text-secondary">
                                <strong>{{ __('serp.result.labels.title') }}</strong> {{ $result->title }}
                            </div>
                        @endif
                    </div>
                @elseif($result->isNotFound())
                    <span class="badge text-bg-warning">{{ __('serp.result.status.not_found') }}</span>
                    <p class="text-secondary mt-3 mb-0">
                        {{ __('serp.result.labels.domain') }} <strong>{{ $result->targetDomain }}</strong>
                        {{ __('serp.result.not_found_suffix', ['depth' => (int) config('serp.google.depth', 100)]) }}
                    </p>
                @else
                    <span class="badge text-bg-danger">{{ __('serp.result.status.error') }}</span>
                    <p class="text-secondary mt-3 mb-0">{{ $result->message ?: __('serp.result.generic_error') }}</p>
                @endif

                @if($result->message && ! $result->isError())
                    <p class="text-secondary mt-2 mb-0">{{ __('serp.result.labels.api') }} {{ $result->message }}</p>
                @endif
            </div>
        </div>
    @endisset
</div>

<script>
    (function () {
        const form = document.getElementById('serpForm');
        const btn = document.getElementById('submitBtn');
        const prog = document.getElementById('progress');
        if (!form || !btn || !prog) return;

        const locationLabel = document.getElementById('location_label');
        const locationCode = document.getElementById('location_code');
        const locationOptions = document.getElementById('locationOptions');
        const locationsUrl = @json(route('serp.locations'));

        let pendingTimer = null;
        let abortController = null;

        function setOptions(items) {
            if (!locationOptions) return;
            locationOptions.innerHTML = '';

            (items || []).forEach(function (item) {
                if (!item || !item.label || !item.value) return;

                const opt = document.createElement('option');
                opt.value = item.label;
                opt.dataset.code = String(item.value);
                locationOptions.appendChild(opt);
            });
        }

        function syncCodeFromLabel(clearIfMissing) {
            if (!locationLabel || !locationCode || !locationOptions) return;

            const label = locationLabel.value;
            const opt = Array.from(locationOptions.options).find(o => o.value === label);
            if (opt) {
                locationCode.value = opt.dataset.code || '';
                return;
            }

            if (clearIfMissing) {
                locationCode.value = '';
            }
        }

        async function fetchLocations(query) {
            if (!locationsUrl) return;

            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            const url = locationsUrl + '?q=' + encodeURIComponent(query) + '&limit=5';
            const res = await fetch(url, {signal: abortController.signal, headers: {'Accept': 'application/json'}});
            if (!res.ok) return;

            const json = await res.json();
            if (!json || !Array.isArray(json.data)) return;

            setOptions(json.data);
            syncCodeFromLabel(true);
        }

        if (locationLabel) {
            locationLabel.addEventListener('input', function () {
                if (!locationCode) return;

                locationCode.value = '';
                syncCodeFromLabel(false);

                const q = locationLabel.value.trim();
                if (pendingTimer) window.clearTimeout(pendingTimer);

                if (q.length < 2) {
                    setOptions([]);
                    return;
                }

                pendingTimer = window.setTimeout(function () {
                    fetchLocations(q).catch(function () {});
                }, 200);
            });

            locationLabel.addEventListener('change', function () {
                syncCodeFromLabel(true);
            });

            syncCodeFromLabel(false);
        }

        form.addEventListener('submit', function () {
            btn.disabled = true;
            prog.textContent = @json(__('serp.form.loading'));
        });
    })();
</script>
</body>
</html>
