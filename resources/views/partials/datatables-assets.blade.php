@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/libs/datatables/dataTables.dataTables.min.css') }}?v={{ filemtime(public_path('assets/libs/datatables/dataTables.dataTables.min.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/datatables.css') }}?v={{ filemtime(public_path('css/datatables.css')) }}">
    @endpush
    @push('scripts')
        <script src="{{ asset('assets/libs/jquery/jquery-3.7.1.min.js') }}?v={{ filemtime(public_path('assets/libs/jquery/jquery-3.7.1.min.js')) }}" defer></script>
        <script src="{{ asset('assets/libs/datatables/dataTables.min.js') }}?v={{ filemtime(public_path('assets/libs/datatables/dataTables.min.js')) }}" defer></script>
        <script src="{{ asset('js/datatables.js') }}?v={{ filemtime(public_path('js/datatables.js')) }}" defer></script>
    @endpush
@endonce
