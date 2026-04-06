<div class="space-y-6">
    @if(empty($skills))
        <p class="text-sm text-gray-500">Ingen kørefærdigheder registreret endnu.</p>
    @else
        @php
            $practiced = array_filter($skills, fn($s) => $s['count'] > 0);
            $notPracticed = array_filter($skills, fn($s) => $s['count'] === 0);
        @endphp

        @if(! empty($practiced))
            <div class="space-y-3">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Øvede færdigheder</h3>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach($practiced as $skill)
                        @php $isApproved = in_array($skill['key'], $completedSkills); @endphp
                        <div class="flex flex-col items-center justify-center gap-1 rounded-xl border p-5 text-center transition-colors
                            {{ $isApproved ? 'border-green-500/30 bg-green-500/5' : 'border-primary/30 bg-primary/5' }}">
                            <p class="text-sm font-semibold">{{ $skill['label'] }}</p>
                            @if($isApproved)
                                <p class="flex items-center gap-1 text-xs font-medium text-green-600">
                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Godkendt
                                </p>
                            @else
                                <p class="text-xs font-medium text-primary">× {{ $skill['count'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(! empty($notPracticed))
            <div class="space-y-3">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Endnu ikke øvet</h3>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach($notPracticed as $skill)
                        @php $isApproved = in_array($skill['key'], $completedSkills); @endphp
                        <div class="flex flex-col items-center justify-center gap-1 rounded-xl border border-border bg-muted/20 p-5 text-center opacity-50 transition-colors">
                            <p class="text-sm font-semibold text-gray-500">{{ $skill['label'] }}</p>
                            @if($isApproved)
                                <p class="flex items-center gap-1 text-xs font-medium text-green-600">
                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Godkendt
                                </p>
                            @else
                                <p class="text-xs text-gray-400">Ikke øvet endnu</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(count(array_filter($skills, fn($s) => $s['count'] > 0)) === 0)
            <p class="text-sm text-gray-500">
                Ingen kørefærdigheder registreret endnu — de vil dukke op her efter din første kørelektion.
            </p>
        @endif
    @endif
</div>
