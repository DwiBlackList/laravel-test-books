@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mt-5">
    <h1 class="mb-4">Book CRUD</h1>
    <div class="mb-4">
        <button class="btn btn-primary" onclick="showForm()">Add New</button>
    </div>
    <table class="table table-bordered" id="bookTable">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Serial Number</th>
                <th>Published At</th>
                <th>Author</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal for CRUD operations -->
<div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Book</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bookId">
                <div class="form-group">
                    <label for="bookTitle">Title</label>
                    <input type="text" id="bookTitle" class="form-control">
                </div>
                <div class="form-group">
                    <label for="serialNumber">Serial Number</label>
                    <input type="text" id="serialNumber" class="form-control">
                </div>
                <div class="form-group">
                    <label for="publishedAt">Published At</label>
                    <input type="date" id="publishedAt" class="form-control">
                </div>
                <div class="form-group">
                    <label for="authorId">Author</label>
                    <select id="bookAuthorId" class="form-control">
                        @foreach($authors as $author)
                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveBook()">Save</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#bookTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/books',
                type: 'GET'
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'title'
                },
                {
                    data: 'serial_number'
                },
                {
                    data: 'published_at'
                },
                {
                    data: 'author.name'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });

    function showForm() {
        $('#bookModalTitle').text('Add New Book');
        $('#bookId').val('');
        $('#bookTitle').val('');
        $('#serialNumber').val('');
        $('#publishedAt').val('');
        $('#bookAuthorId').val('');
        $('#bookModal').modal('show');
    }

    function saveBook() {
        const id = $('#bookId').val();
        const title = $('#bookTitle').val();
        const serialNumber = $('#serialNumber').val();
        const publishedAt = $('#publishedAt').val();
        const authorId = $('#bookAuthorId').val();

        const url = id ? `/books/${id}` : '/books';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                title: title,
                serial_number: serialNumber,
                published_at: publishedAt,
                author_id: authorId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('#bookModal').modal('hide');
                $('#bookTable').DataTable().ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Book has been saved successfully.'
                });
            },
            error: function(xhr, status, error) {
                // Cek jika response mengandung error validation dari Laravel
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';

                    // Loop setiap error dan tambahkan ke pesan error
                    for (let key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            errorMessage += errors[key][0] + '\n';
                        }
                    }

                    // Tampilkan pesan kesalahan di SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage
                    });
                } else {
                    // Tampilkan error umum jika bukan error validasi
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the data.'
                    });
                }
            }
        });
    }

    function editBook(id) {
        $.ajax({
            url: `/books/${id}`,
            method: 'GET',
            success: function(data) {
                $('#bookModalTitle').text('Edit Book');
                $('#bookId').val(data.id);
                $('#bookTitle').val(data.title);
                $('#serialNumber').val(data.serial_number);
                $('#publishedAt').val(data.published_at);
                $('#bookAuthorId').val(data.author_id);
                $('#bookModal').modal('show');
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching the book data.'
                });
            }
        });
    }

    function deleteBook(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/books/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#bookTable').DataTable().ajax.reload();
                        Swal.fire(
                            'Deleted!',
                            'The book has been deleted.',
                            'success'
                        );
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the book.'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection