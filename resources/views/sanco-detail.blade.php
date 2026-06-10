<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $entity->name }} — Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h1 class="h3 fw-bold mb-1">{{ $entity->name }}</h1>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span class="badge bg-info">{{ $entity->schema }}</span>
                    @if ($entity->countries)<span>{{ strtoupper($entity->countries) }}</span>@endif
                    @if ($entity->birth_date)<span>· {{ $entity->birth_date }}</span>@endif
                    @if ($entity->gender)<span>· {{ ucfirst($entity->gender) }}</span>@endif
                </div>
            </div>
            <a href="{{ $opensanctionsUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">OpenSanctions ↗</a>
        </div>

        {{-- Tags --}}
        <div class="mb-3">
            @foreach ($tags as $tag)
                @php $tl = strtolower($tag); @endphp
                <span class="badge me-1 {{ str_contains($tl, 'pep') ? 'bg-primary' : 'bg-danger' }}">{{ $tag }}</span>
            @endforeach
        </div>

        <div class="row g-4">
            {{-- Left: Main Info --}}
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Information</div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @php
                                    $rows = [
                                        'Place of Birth' => $entity->birth_place,
                                        'Nationality' => $entity->nationality,
                                        'Position' => $entity->position,
                                        'Notes' => $entity->notes,
                                        'Address' => $entity->addresses,
                                        'Email' => $entity->emails,
                                        'Identifiers' => $entity->identifiers,
                                    ];
                                    // Add properties JSON fields
                                    if (!empty($properties)) {
                                        foreach (['website', 'education', 'religion', 'citizenship', 'political', 'sourceUrl', 'wikidataId', 'wikipediaUrl'] as $key) {
                                            if (!empty($properties[$key])) {
                                                $label = ucfirst(preg_replace('/([A-Z])/', ' $1', $key));
                                                $val = is_array($properties[$key]) ? implode('; ', $properties[$key]) : $properties[$key];
                                                $rows[$label] = $val;
                                            }
                                        }
                                    }
                                @endphp
                                @foreach ($rows as $label => $val)
                                    @if ($val)
                                    <tr>
                                        <td class="text-muted fw-semibold text-nowrap align-top" style="width:160px">{{ $label }}</td>
                                        <td class="text-break">{{ $val }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Names --}}
                @if ($entity->aliases || $entity->weak_aliases)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Names &amp; Aliases</div>
                    <div class="card-body">
                        @if ($entity->aliases)
                            <div class="mb-3">
                                <div class="text-muted small fw-semibold text-uppercase mb-2">Also Known As</div>
                                @foreach (explode('; ', $entity->aliases) as $a)
                                    <span class="badge bg-secondary me-1 mb-1">{{ trim($a) }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if ($entity->weak_aliases)
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase mb-2">Weak References</div>
                                @foreach (explode('; ', $entity->weak_aliases) as $a)
                                    <span class="badge bg-light text-dark me-1 mb-1">{{ trim($a) }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Right: Details --}}
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Details</div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small text-muted text-uppercase">Entity ID</th>
                                    <th class="small text-muted text-uppercase">First Seen</th>
                                    <th class="small text-muted text-uppercase">Last Updated</th>
                                    <th class="small text-muted text-uppercase">Dataset</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-monospace small">{{ $entity->entity_id }}</td>
                                    <td class="small">{{ $entity->first_seen ? \Carbon\Carbon::parse($entity->first_seen)->format('d M Y') : '-' }}</td>
                                    <td class="small">{{ $entity->last_change ? \Carbon\Carbon::parse($entity->last_change)->format('d M Y') : '-' }}</td>
                                     <td class="small">{{ $entity->dataset_title ?? $dataset?->title ?? $entity->dataset_name }}</td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Last Seen --}}
                @if ($entity->last_seen)
                <div class="card shadow-sm mb-4">
                    <div class="card-body small text-muted">
                        <strong>Last Seen:</strong> {{ \Carbon\Carbon::parse($entity->last_seen)->format('d M Y H:i') }}
                    </div>
                </div>
                @endif

                <a href="{{ $opensanctionsUrl }}" target="_blank" class="btn btn-primary w-100">
                    Open Full Profile on OpenSanctions ↗
                </a>
            </div>
        </div>
    </div>
</body>
</html>
