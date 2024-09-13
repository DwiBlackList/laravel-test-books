@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mt-5">
    <h1 class="mb-4">Author CRUD</h1>
    <div class="mb-4">
        <button class="btn btn-primary" onclick="showForm()">Add New</button>
    </div>
    <table class="table table-bordered" id="authorTable">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal for CRUD operations -->
<div class="modal fade" id="authorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Author</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="authorId">
                <div class="form-group">
                    <label for="nama">Name</label>
                    <input type="text" id="name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveAuthor()">Save</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#authorTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/authors',
                type: 'GET'
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
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
        $('#modalTitle').text('Add New Author');
        $('#authorId').val('');
        $('#name').val('');
        $('#email').val('');
        $('#authorModal').modal('show');
    }

    function saveAuthor() {
        const id = $('#authorId').val();
        const name = $('#name').val();
        const email = $('#email').val();

        const url = id ? `/authors/${id}` : '/authors';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                name: name,
                email: email,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('#authorModal').modal('hide');
                $('#authorTable').DataTable().ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Data has been saved successfully.'
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

    function editAuthor(id) {
        $.ajax({
            url: `/authors/${id}`,
            method: 'GET',
            success: function(data) {
                $('#modalTitle').text('Edit Author');
                $('#authorId').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#authorModal').modal('show');
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching the data.'
                });
            }
        });
    }

    function deleteAuthor(id) {
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
                    url: `/authors/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#authorTable').DataTable().ajax.reload();
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        );
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the data.'
                        });
                    }
                });
            }
        });
    }

    // function closemodal(id) {
    //     $('#authorModal').modal('hidden');
    // }
</script>
@endsection