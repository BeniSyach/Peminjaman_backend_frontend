@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">User Manajemen /</span> Pengguna
        </h4>

        <div class="card">
            <h5 class="card-header d-flex justify-content-between">
                Data Pengguna
                <button class="btn btn-primary" id="btnTambah">Tambah Pengguna</button>
            </h5>
            <div class="table-responsive text-nowrap">
                <table class="table" id="customerTable">
                    <thead>
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>level</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No. Hp</th>
                            <th>Tanggal Mendaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <!-- Data akan diisi oleh DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="modalCustomer" tabindex="-1" aria-labelledby="modalCustomerLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCustomerLabel">Tambah Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCustomer">
                        <input type="hidden" id="customerId">
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" required>
                        </div>
                        <div class="mb-3 rolelevel">
                            <label for="PilihRole" class="form-label">Role</label>
                            <select class="form-select" id="role" aria-label="PilihRole">
                                <option selected>Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="petugas">Petugas</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="no_hp" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Ambil token dari localStorage
            const token = localStorage.getItem('token');

            const user = JSON.parse(localStorage.getItem('user'));

            if (user && user.role !== 'admin') {
                $('#role').closest('.rolelevel').remove(); // Menghapus elemen <div> yang membungkus select
            }

            // Jika token tidak ada, redirect ke halaman login
            if (!token) {
                window.location.href = '/';
                return;
            }

            var table = $('#customerTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/api/users',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    // Mengubah format data untuk diproses DataTable
                    dataSrc: function(json) {

                        console.log('data json', json.data);
                        // Mengembalikan array data
                        return json.data || [];
                    },
                    // Menangani respons untuk informasi paginasi
                    dataFilter: function(data) {
                        var json = JSON.parse(data);

                        // Memodifikasi respons untuk format yang diharapkan DataTables
                        var modifiedJson = {
                            draw: 1,
                            recordsTotal: json.data.total,
                            recordsFiltered: json.data.total,
                            data: json.data.data || []
                        };

                        return JSON.stringify(modifiedJson);
                    },
                    // Menyesuaikan parameter yang dikirim ke server
                    data: function(d) {
                        console.log('data d', d);
                        return {
                            page: (d.start / d.length) + 1,
                            per_page: d.length,
                            search: d.search.value
                        };
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'role'
                    },
                    {
                        data: 'nik'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'no_hp'
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-sm btn-warning btnEdit" data-id="${row.id}">Edit</button>
                        <button class="btn btn-sm btn-danger btnDelete" data-id="${row.id}">Hapus</button>
                        `;
                        }
                    }
                ],
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                    infoPostFix: "",
                    loadingRecords: "Memuat data...",
                    zeroRecords: "Tidak ditemukan data yang sesuai",
                    emptyTable: "Tidak ada data yang tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                }
            });

            // **TOMBOL TAMBAH CUSTOMER**
            $('#btnTambah').click(function() {
                $('#formCustomer')[0].reset();
                $('#customerId').val('');
                $('#modalCustomerLabel').text('Tambah Customer');
                $('#modalCustomer').modal('show');
            });

            // **SUBMIT FORM (TAMBAH/EDIT)**
            $('#formCustomer').submit(function(e) {
                e.preventDefault();
                const id = $('#customerId').val();
                const method = id ? 'PUT' : 'POST';
                const url = id ? `/api/users/${id}` : '/api/register';

                // Ambil nilai password dan password_confirmation
                let password = $('#password').val();
                let passwordConfirmation = $('#password_confirmation').val();

                // Buat objek data
                let data = {
                    nik: $('#nik').val(),
                    nama: $('#nama').val(),
                    alamat: $('#alamat').val(),
                    no_hp: $('#no_hp').val(),
                    role: 'customer'
                };

                // Tambahkan password hanya jika tidak kosong
                if (password !== '') {
                    data.password = password;
                    data.password_confirmation = passwordConfirmation;
                }

                if (user && user.role === 'admin') {
                    data.role = $('#role').val();
                }

                $.ajax({
                    url: url,
                    type: method,
                    contentType: 'application/json',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: JSON.stringify(data),
                    success: function(response) {
                        $('#modalCustomer').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload(); // REFRESH halaman
                        });
                    },
                    error: function() {
                        $('#modalCustomer').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menyimpan data!',
                        });
                    }
                });
            });


            // **EDIT CUSTOMER**
            $('#customerTable').on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `/api/users/${id}`,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        $('#customerId').val(response.data.id);
                        $('#nik').val(response.data.nik);
                        $('#nama').val(response.data.nama);
                        $('#alamat').val(response.data.alamat);
                        $('#no_hp').val(response.data.no_hp);

                        $('#password').removeAttr('required');
                        $('#password_confirmation').removeAttr('required');
                        $('#modalCustomerLabel').text('Edit Customer');
                        $('#modalCustomer').modal('show');
                    }
                });
            });

            // **HAPUS CUSTOMER DENGAN KONFIRMASI SWEETALERT**
            $('#customerTable').on('click', '.btnDelete', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data akan dihapus secara permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/users/${id}`,
                            type: 'DELETE',
                            headers: {
                                'Authorization': 'Bearer ' + token
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Customer berhasil dihapus!',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload(); // REFRESH halaman
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data!',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
