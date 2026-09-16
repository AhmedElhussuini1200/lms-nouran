@extends('dashboard.partials.master')
@section('content')
<div class="card mb-7"><div class="card-body py-6 px-6">
<h2 class="fw-bold mb-2">{{ __('سلة المحذوفات') }}</h2>
<p class="text-muted mb-0">{{ __('المستخدمون المحذوفون — استرجاع أو حذف نهائي') }}</p>
</div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-4 mb-0" id="trash-table">
<thead><tr class="fw-bold text-muted"><th>#</th><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Actions') }}</th></tr></thead>
<tbody></tbody>
</table></div></div></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#trash-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.trash', 'Admin') }}",
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: null, orderable: false, searchable: false, render: function(data, type, row) {
                return `<div class="d-flex gap-2 justify-content-end">
                    <a href="{{ url('dashboard/trash/Admin') }}/${row.id}" class="btn btn-sm btn-light-success restore-btn">{{ __('Restore') }}</a>
                    <button class="btn btn-sm btn-light-danger delete-btn" data-id="${row.id}">{{ __('Delete') }}</button>
                </div>`;
            }}
        ],
        language: { url: "{{ asset('assets/js/datatable-translations/' . (isArabic() ? 'ar.json' : 'en.json')) }}" }
    });

    $(document).on('click', '.delete-btn', function() {
        if (!confirm("{{ __('هل أنت متأكد؟') }}")) return;
        let id = $(this).data('id');
        $.ajax({
            url: "{{ url('dashboard/trash/Admin') }}/" + id,
            type: 'DELETE',
            data: { _token: "{{ csrf_token() }}" },
            success: () => $('#trash-table').DataTable().ajax.reload()
        });
    });
});
</script>
@endpush
