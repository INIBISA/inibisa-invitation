@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.dataTables.min.css') }}?v={{ filemtime(public_path('vendor/datatables/dataTables.dataTables.min.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/datatables.css') }}?v={{ filemtime(public_path('css/datatables.css')) }}">
    @endpush
    @push('scripts')
        <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}?v={{ filemtime(public_path('vendor/jquery/jquery-3.7.1.min.js')) }}" defer></script>
        <script src="{{ asset('vendor/datatables/dataTables.min.js') }}?v={{ filemtime(public_path('vendor/datatables/dataTables.min.js')) }}" defer></script>
        <script src="{{ asset('js/datatables.js') }}?v={{ filemtime(public_path('js/datatables.js')) }}" defer></script>
    @endpush
@endonce
