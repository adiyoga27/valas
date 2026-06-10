@extends('layouts.admin')
@section('title', 'Cek Nama PEP & DTTOT')
@section('heading', 'Cek Nama PEP & DTTOT')
@section('content')
<div class="space-y-4">
    <p class="text-sm text-gray-500">{{ number_format($totalEntities, 0, ',', '.') }} entitas tersedia. <a href="{{ route('admin.sanco.datasets.index') }}" class="text-amber-600 hover:underline">Dataset</a></p>

    <form method="GET" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 flex items-end gap-3">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 mb-1">Nama / Kata Kunci</label>
            <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Masukkan nama orang atau perusahaan..." minlength="2" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
        </div>
        <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">Cari</button>
    </form>

    @if($searched)
        @if($error)
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-600">{{ $error }}</div>
        @elseif(!empty($results))
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Name</th><th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Tags</th><th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Birth</th><th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Action</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($results as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5"><a href="{{ $row['detail_url'] }}" class="text-sm font-medium text-amber-600 hover:underline">{{ $row['caption'] }}</a><span class="text-xs text-gray-400 ml-1">({{ $row['schema'] }})</span></td>
                                <td class="px-4 py-2.5">
                                    @php $tags = collect(explode(', ', $row['datasets']))->filter()->unique(); @endphp
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tags as $tag)
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                                @if(stripos($tag, 'pep') !== false) bg-blue-100 text-blue-700
                                                @elseif(stripos($tag, 'terror') !== false || stripos($tag, 'sanction') !== false || stripos($tag, 'ofac') !== false) bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-600 @endif">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-gray-500">{{ $row['birth_date'] !== '-' ? $row['birth_date'] : '-' }}</td>
                                <td class="px-4 py-2.5 text-right"><a href="{{ $row['detail_url'] }}" class="text-amber-600 hover:underline text-sm font-medium">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-gray-500">{{ count($results) }} hasil @if($source) ({{ $source }}) @endif</p>
        @else
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 text-sm text-gray-500">Tidak ada hasil untuk "{{ $keyword }}".</div>
        @endif
    @endif
</div>
@endsection
