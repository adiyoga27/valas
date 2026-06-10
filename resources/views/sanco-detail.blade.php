<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $entity->name }} — Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #333; }
        .page-header { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; padding: 2.5rem 0; margin-bottom: 2rem; border-bottom: 4px solid #ffc107; }
        .badge-schema { background-color: rgba(255, 255, 255, 0.2); color: white; border: 1px solid rgba(255,255,255,0.4); font-weight: 500; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); margin-bottom: 1.5rem; }
        .card-header { background-color: #fff; border-bottom: 1px solid #edf2f7; padding: 1.25rem 1.5rem; font-weight: 600; font-size: 1.1rem; border-radius: 12px 12px 0 0 !important; color: #2d3748; }
        .table th { font-weight: 600; color: #4a5568; }
        .table td { color: #2d3748; }
        .prop-label { width: 35%; font-weight: 600; color: #4a5568; background-color: #f8f9fa; padding: 0.75rem 1rem !important; vertical-align: top; border-right: 1px solid #edf2f7; }
        .prop-value { padding: 0.75rem 1rem !important; }
        .table-properties { border: 1px solid #edf2f7; border-radius: 8px; overflow: hidden; border-collapse: separate; border-spacing: 0; }
        .table-properties tr:last-child td, .table-properties tr:last-child th { border-bottom: none; }
        .tag-badge { font-weight: 500; padding: 0.4em 0.8em; }
    </style>
</head>
<body>
    <div class="page-header shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold mb-2">{{ $entity->name }}</h1>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="badge badge-schema rounded-pill px-3 py-2"><i class="bi bi-box-seam me-1"></i> {{ $entity->schema }}</span>
                        @if ($entity->countries)
                            <span class="badge badge-schema rounded-pill px-3 py-2"><i class="bi bi-globe me-1"></i> {{ strtoupper($entity->countries) }}</span>
                        @endif
                        @if ($entity->birth_date)
                            <span class="text-white-50"><i class="bi bi-calendar3 me-1"></i> {{ $entity->birth_date }}</span>
                        @endif
                        @if ($entity->gender)
                            <span class="text-white-50"><i class="bi bi-person me-1"></i> {{ ucfirst($entity->gender) }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ $opensanctionsUrl }}" target="_blank" class="btn btn-light btn-sm fw-semibold shadow-sm text-primary">
                    <i class="bi bi-box-arrow-up-right me-1"></i> OpenSanctions
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        @if ($tags->count() > 0)
        <div class="mb-4">
            <h5 class="fw-semibold mb-3 text-muted">Risk Tags</h5>
            @foreach ($tags as $tag)
                @php $tl = strtolower($tag); @endphp
                <span class="badge tag-badge me-2 mb-2 {{ str_contains($tl, 'pep') ? 'bg-primary' : (str_contains($tl, 'sanction') ? 'bg-danger' : 'bg-warning text-dark') }}">
                    <i class="bi bi-tag-fill me-1"></i> {{ $tag }}
                </span>
            @endforeach
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Main Information Table --}}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-info-circle text-primary me-2"></i> Detailed Information
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-properties w-100 mb-0">
                            <tbody>
                                @php
                                    $rows = [];
                                    if ($entity->birth_place) $rows['Place of Birth'] = $entity->birth_place;
                                    if ($entity->nationality) $rows['Nationality'] = $entity->nationality;
                                    if ($entity->position) $rows['Position'] = $entity->position;
                                    if ($entity->notes) $rows['Notes'] = $entity->notes;
                                    if ($entity->addresses) $rows['Address'] = $entity->addresses;
                                    if ($entity->emails) $rows['Email'] = $entity->emails;
                                    if ($entity->identifiers) $rows['Identifiers'] = $entity->identifiers;

                                    // Add ALL properties dynamically
                                    if (!empty($properties) && is_array($properties)) {
                                        foreach ($properties as $key => $val) {
                                            if (in_array($key, ['name', 'alias', 'weakAlias', 'country', 'birthDate', 'birthPlace', 'gender', 'nationality', 'position', 'notes', 'addressEntity', 'email', 'idNumber'])) {
                                                continue; // skip common duplicates
                                            }
                                            $label = ucfirst(preg_replace('/([a-z])([A-Z])/', '$1 $2', $key));
                                            $valStr = is_array($val) ? implode('; ', $val) : $val;
                                            if ($valStr) {
                                                $rows[$label] = $valStr;
                                            }
                                        }
                                    }
                                @endphp
                                @forelse ($rows as $label => $val)
                                    <tr>
                                        <td class="prop-label">{{ $label }}</td>
                                        <td class="prop-value">
                                            @if(str_starts_with($val, 'http'))
                                                <a href="{{ $val }}" target="_blank" class="text-primary text-decoration-none">{{ $val }}</a>
                                            @else
                                                {!! nl2br(e(str_replace('; ', "\n• ", "• " . $val))) !!}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">No detailed information available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Names and Aliases --}}
                @if ($entity->aliases || $entity->weak_aliases)
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-people text-success me-2"></i> Names & Aliases
                    </div>
                    <div class="card-body">
                        @if ($entity->aliases)
                            <div class="mb-4">
                                <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size: 0.8rem;">Also Known As (AKA)</h6>
                                <div class="d-flex flex-wrap gap-2">
                                @foreach (explode('; ', $entity->aliases) as $a)
                                    <span class="badge bg-light text-dark border px-3 py-2">{{ trim($a) }}</span>
                                @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($entity->weak_aliases)
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size: 0.8rem;">Weak References</h6>
                                <div class="d-flex flex-wrap gap-2">
                                @foreach (explode('; ', $entity->weak_aliases) as $a)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 px-3 py-2">{{ trim($a) }}</span>
                                @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                {{-- Metadata Card --}}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-database text-secondary me-2"></i> Record Metadata
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush rounded-bottom-3">
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-muted small text-uppercase">Entity ID</div>
                                    <span class="font-monospace text-dark">{{ $entity->entity_id }}</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-muted small text-uppercase">Dataset Source</div>
                                    <span class="text-dark">{{ $dataset_titles }}</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-muted small text-uppercase">First Seen</div>
                                    <span class="text-dark">{{ $entity->first_seen ? \Carbon\Carbon::parse($entity->first_seen)->format('d M Y') : '-' }}</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-muted small text-uppercase">Last Updated</div>
                                    <span class="text-dark">{{ $entity->last_change ? \Carbon\Carbon::parse($entity->last_change)->format('d M Y') : '-' }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- External Link Card --}}
                <div class="card bg-primary text-white text-center">
                    <div class="card-body py-4">
                        <i class="bi bi-shield-check display-4 mb-3 d-block opacity-75"></i>
                        <h5 class="fw-bold mb-3">Verify on OpenSanctions</h5>
                        <p class="small opacity-75 mb-4">View the full profile, family relations, and corporate structures directly on the OpenSanctions platform.</p>
                        <a href="{{ $opensanctionsUrl }}" target="_blank" class="btn btn-light w-100 fw-bold text-primary">
                            Open Profile <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
