@extends('layouts.base')

@section('title', 'Prediksi PTN')

@section('styles')
    <link href="{{ asset('css/prediksi-ptn.css') }}" rel="stylesheet">
@endsection

@section('body_class', 'page-prediksi-ptn')

@section('content')
    @php
        $currentPtnId = old('ptn_id', optional($selectedPtn)->id);
        $currentProdiId = old('prodi_id', optional($selectedProdi)->id);
    @endphp

    <section class="prediksi-hero">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="section-label">SNBP / PTN</span>
                <h1 class="page-title">Prediksi Masuk PTN</h1>
                <p class="page-subtitle">Kalkulasi berdasarkan grade rapor dan mapel relevan dengan program studi pilihan Anda.</p>
            </div>

            <form method="POST" action="{{ route('prediksi.ptn.submit') }}" class="prediction-form">
                @csrf

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Universitas</label>
                        <select name="ptn_id" id="ptn_id" class="form-select" required data-selected="{{ $currentPtnId ?? '' }}">
                            <option value="">Pilih universitas</option>
                            @foreach ($ptns as $ptn)
                                <option value="{{ $ptn->id }}" {{ (string) $currentPtnId === (string) $ptn->id ? 'selected' : '' }}>
                                    {{ $ptn->ptn }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Program Studi</label>
                        <select name="prodi_id" id="prodi_id" class="form-select" required data-selected="{{ $currentProdiId ?? '' }}">
                            <option value="">Pilih program studi</option>
                            @if ($selectedPtn)
                                @foreach ($selectedPtn->prodi as $prodi)
                                    <option value="{{ $prodi->id }}" {{ (string) $currentProdiId === (string) $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->prodi }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="card shadow-sm border-0 table-card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0 prediction-table">
                                <thead>
                                    <tr>
                                        <th>Mata Pelajaran</th>
                                        @foreach ($semesterList as $semester)
                                            <th class="text-center">Semester {{ $semester }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mapelList as $mapel)
                                        <tr>
                                            <td class="fw-semibold subject-name">{{ $mapel }}</td>
                                            @foreach ($semesterList as $semester)
                                                <td>
                                                    <input
                                                        type="number"
                                                        name="nilai[{{ $mapel }}][{{ $semester }}]"
                                                        class="form-control text-center grade-input"
                                                        min="0"
                                                        max="100"
                                                        step="0.1"
                                                        value="{{ old('nilai.' . $mapel . '.' . $semester, '') }}"
                                                        placeholder="0"
                                                    >
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary-custom px-5 py-3">
                        <i class="bi bi-bar-chart-fill me-2"></i>Prediksi
                    </button>
                </div>
            </form>

            @if ($result)
                <div class="result-box mt-5">
                    <div class="result-title">HASIL PREDIKSI SNBP</div>
                    <div class="result-row">
                        <span>Skor Anda</span>
                        <strong>{{ number_format($result['skor'], 2) }}</strong>
                    </div>
                    <div class="result-row">
                        <span>Batas Minimum</span>
                        <strong>{{ number_format($result['batas_minimum'], 2) }}</strong>
                    </div>
                    <div class="result-row">
                        <span>Selisih</span>
                        <strong>{{ $result['selisih'] >= 0 ? '+' : '' }}{{ number_format($result['selisih'], 2) }}</strong>
                    </div>
                    <div class="result-divider"></div>
                    <div class="result-row">
                        <span>Peluang lolos</span>
                        <strong>{{ number_format($result['peluang_lolos'], 2) }}%</strong>
                    </div>
                    <div class="result-row">
                        <span>Tingkat Persaingan</span>
                        <strong>{{ number_format($result['tingkat_persaingan'], 2) }}%</strong>
                    </div>
                    <div class="result-status {{ $result['status'] }}">{{ $result['status_label'] }}</div>
                </div>

                <div class="alternatif-box mt-4">
                    <h3 class="alternatif-title">Alternatif terbaik</h3>

                    @if (!empty($result['alternatif']))
                        <div class="list-group list-group-flush">
                            @foreach ($result['alternatif'] as $alternatif)
                                <div class="list-group-item alternatif-item">
                                    <div>
                                        <div class="alternatif-name">{{ $alternatif['prodi'] }}</div>
                                        <small class="text-muted">Grade minimum: {{ number_format($alternatif['grade'], 2) }} • Persaingan: {{ number_format($alternatif['persen'], 2) }}%</small>
                                    </div>
                                    <div class="alternatif-score">
                                        <span>{{ number_format($alternatif['skor'], 2) }}</span>
                                        <small>Skor</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">Belum ada alternatif yang memenuhi threshold grade pada kampus ini.</div>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        window.ptnProdiData = @json($ptns->mapWithKeys(function ($ptn) {
            return [$ptn->id => $ptn->prodi->map(function ($prodi) {
                return ['id' => $prodi->id, 'prodi' => $prodi->prodi];
            })];
        }));
    </script>
    <script src="{{ asset('js/prediksi-ptn.js') }}"></script>
@endsection
