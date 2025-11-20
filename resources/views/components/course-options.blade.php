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
    <optgroup label="CED">
        <option value="Secondary" {{ ($selected == 'Secondary') ? 'selected' : '' }}>Secondary</option>
        <option value="Bsed" {{ ($selected == 'Bsed') ? 'selected' : '' }}>Bsed</option>
        <option value="Filipino" {{ ($selected == 'Filipino') ? 'selected' : '' }}>Filipino</option>
        <option value="Math" {{ ($selected == 'Math') ? 'selected' : '' }}>Math</option>
        <option value="Social studies" {{ ($selected == 'Social studies') ? 'selected' : '' }}>Social studies</option>
        <option value="btvted" {{ ($selected == 'btvted') ? 'selected' : '' }}>btvted</option>
        <option value="btled" {{ ($selected == 'btled') ? 'selected' : '' }}>btled</option>
        <option value="bee" {{ ($selected == 'bee') ? 'selected' : '' }}>bee</option>
    </optgroup>

    <optgroup label="BPA">
        <option value="BPA" {{ ($selected == 'BPA') ? 'selected' : '' }}>BPA</option>
    </optgroup>

    <optgroup label="CRIM">
        <option value="CRIM" {{ ($selected == 'CRIM') ? 'selected' : '' }}>CRIM</option>
    </optgroup>

    <optgroup label="CAT/TECHSOC">
        <option value="BIT" {{ ($selected == 'BIT') ? 'selected' : '' }}>BIT</option>
        <option value="BSIT" {{ ($selected == 'BSIT') ? 'selected' : '' }}>BSIT</option>
        <option value="ENTREP" {{ ($selected == 'ENTREP') ? 'selected' : '' }}>ENTREP</option>
    </optgroup>
@endif
