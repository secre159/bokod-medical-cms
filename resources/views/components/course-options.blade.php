{{-- Shared course/program options --}}
@php
    $selected = $selected ?? null;
    try {
        $departments = \App\Models\Department::with(['courses' => function($q){
            $q->where('active', true)->orderBy('name');
        }])->where('active', true)->orderBy('name')->get();
    } catch (\Throwable $e) {
        $departments = collect();
    }
@endphp

@if($departments->count())
    @foreach($departments as $dept)
        <optgroup label="{{ $dept->name }}">
            @foreach($dept->courses as $c)
                <option value="{{ $c->name }}" {{ ($selected == $c->name) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </optgroup>
    @endforeach
@else
    <optgroup label="Education">
        <option value="Major in English" {{ ($selected == 'Major in English') ? 'selected' : '' }}>Major in English</option>
        <option value="Major in Filipino" {{ ($selected == 'Major in Filipino') ? 'selected' : '' }}>Major in Filipino</option>
        <option value="Social Science" {{ ($selected == 'Social Science') ? 'selected' : '' }}>Social Science</option>
    </optgroup>

    <optgroup label="Criminology">
        <option value="Criminology" {{ ($selected == 'Criminology') ? 'selected' : '' }}>Criminology</option>
        <option value="BPA" {{ ($selected == 'BPA') ? 'selected' : '' }}>BPA</option>
    </optgroup>

    <optgroup label="CAT">
        <option value="BSIT" {{ ($selected == 'BSIT') ? 'selected' : '' }}>BSIT</option>
        <option value="Entrep" {{ ($selected == 'Entrep') ? 'selected' : '' }}>Entrep</option>
        <option value="BIT" {{ ($selected == 'BIT') ? 'selected' : '' }}>BIT</option>
    </optgroup>
@endif
