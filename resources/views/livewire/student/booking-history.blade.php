@php
$statusLabels = [
    'scheduled'  => 'Planlagt',
    'completed'  => 'Gennemført',
    'cancelled'  => 'Annulleret',
    'no_show'    => 'Udeblevet',
];

$skillLabels = [
    'parking'        => 'Parkering',
    'motorvej'       => 'Motorvej',
    'roundabouts'    => 'Rundkørsel',
    'city_driving'   => 'Bykørsel',
    'overtaking'     => 'Overhaling',
    'reversing'      => 'Bakring',
    'lane_change'    => 'Filskifte',
    'emergency_stop' => 'Nødstop',
];
@endphp

<div class="space-y-4">
    @if(empty($pastBookings))
        <p class="text-sm text-gray-500">Ingen tidligere bookinger endnu.</p>
    @else
        <div class="divide-y rounded-xl border">
            @foreach($pastBookings as $row)
                <div class="px-4 py-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium">{{ $row['type_label'] }}</p>
                            <p class="text-xs text-gray-500">{{ $row['range_label'] }}</p>
                            @if($row['instructor_name'])
                                <p class="text-xs text-gray-400">{{ $row['instructor_name'] }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-normal">
                                {{ $statusLabels[$row['status']] ?? $row['status'] }}
                            </span>
                            @if($row['attended'] === true)
                                <span class="text-xs font-medium text-green-600">Mødt</span>
                            @elseif($row['attended'] === false)
                                <span class="text-xs font-medium text-red-500">Ikke mødt</span>
                            @endif
                        </div>
                    </div>

                    @if(! empty($row['driving_skills']))
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach($row['driving_skills'] as $skill)
                                <span class="inline-flex items-center rounded-full border border-primary/30 bg-primary/5 px-2.5 py-0.5 text-xs font-medium text-primary">
                                    {{ $skillLabels[$skill] ?? $skill }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($row['instructor_note'])
                        <blockquote class="mt-2 border-l-2 border-gray-200 pl-3 text-sm italic text-gray-500">
                            {{ $row['instructor_note'] }}
                        </blockquote>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
