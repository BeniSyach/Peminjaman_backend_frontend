@extends('layouts.app')

@section('title', 'peminjaman')

@section('content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Sistem peminjamanan /</span> peminjamanan
        </h4>

        <div class="card">
            <h5 class="card-header d-flex justify-content-between">
                Data peminjamanan
                <button class="btn btn-primary" id="btnTambah">Tambah peminjamanan</button>
            </h5>
            <div class="table-responsive text-nowrap">
                <table class="table" id="PeminjamanTable">
                    <thead>
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>kode</th>
                            <th>status</th>
                            <th>Nama peminjaman</th>
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
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
    <div class="modal fade" id="modalPeminjaman" tabindex="-1" aria-labelledby="modalPeminjamanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPeminjamanLabel">Tambah Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPeminjaman">
                        <input type="hidden" id="PeminjamanId">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Pilih Peminjaman</label>
                            <select id="user_id">
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="buku_id" class="form-label">Pilih Buku</label>
                            <select id="buku_id">
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                            <input type="date" class="form-control" id="tanggal_pinjam" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                            <input type="date" class="form-control" id="tanggal_kembali" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // Ambil token dari localStorage
            const token = localStorage.getItem('token');

            // Jika token tidak ada, redirect ke halaman login
            if (!token) {
                window.location.href = '/';
                return;
            }

            var table = $('#PeminjamanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/api/peminjaman',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    // Mengubah format data untuk diproses DataTable
                    dataSrc: function(json) {
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
                        data: 'kode_peminjaman'
                    },
                    {
                        data: 'status',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            if (String(data).toLowerCase() === 'dikembalikan') {
                                return `<span class="badge text-bg-primary">Dikembalikan</span>`;
                            }
                            return data ? data : '-'; // Jika data kosong, tampilkan "-"
                        }
                    },
                    {
                        data: 'user.nama', // Ambil nama user dari nested object
                        defaultContent: '-' // Untuk menghindari error jika user null
                    },
                    {
                        data: 'buku.judul', // Ambil judul buku dari nested object
                        defaultContent: '-'
                    },
                    {
                        data: 'tanggal_pinjam',
                        render: function(data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'tanggal_kembali',
                        render: function(data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let buttons = `
                            <button class="btn btn-sm btn-warning btnEdit" data-id="${row.id}">Edit</button>
                            <button class="btn btn-sm btn-danger btnDelete" data-id="${row.id}">Hapus</button>
                        `;
                            if (String(row.status).toLowerCase() !== 'dikembalikan') {
                                buttons +=
                                    `<button class="btn btn-sm btn-info btnKembali" data-id="${row.id}">Kembalikan</button>`;
                            }

                            return buttons;
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

            // **TOMBOL TAMBAH Peminjaman**
            $('#btnTambah').click(function() {
                $('#formPeminjaman')[0].reset();
                $('#PeminjamanId').val('');
                $('#modalPeminjamanLabel').text('Tambah Peminjaman');
                $('#modalPeminjaman').modal('show');
            });

            function generateKodePeminjaman() {
                return 'PMJ-' + Math.floor(100000 + Math.random() * 900000);
            }

            // **SUBMIT FORM (TAMBAH/EDIT)**
            $('#formPeminjaman').submit(function(e) {
                e.preventDefault();
                const id = $('#PeminjamanId').val();
                const method = 'POST';
                const url = id ? `/api/peminjaman/${id}?_method=patch` : '/api/peminjaman';
                const data = {
                    kode_peminjaman: generateKodePeminjaman(),
                    user_id: $('#user_id').val(),
                    buku_id: $('#buku_id').val(),
                    tanggal_pinjam: $('#tanggal_pinjam').val(),
                    tanggal_kembali: $('#tanggal_kembali').val()
                };

                $.ajax({
                    url: url,
                    type: method,
                    contentType: 'application/json',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: JSON.stringify(data),
                    success: function(response) {
                        $('#modalPeminjaman').modal('hide');
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
                        $('#modalPeminjaman').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menyimpan data!',
                        });
                    }
                });
            });

            // **EDIT Peminjaman**
            $('#PeminjamanTable').on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `/api/peminjaman/${id}`,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        $('#PeminjamanId').val(response.id);
                        $('#user_id').val(response.user_id);
                        $('#buku_id').val(response.buku_id);
                        $('#tanggal_pinjam').val(response.tanggal_pinjam);
                        $('#tanggal_kembali').val(response.tanggal_kembali);

                        $('#modalPeminjamanLabel').text('Edit Peminjaman');
                        $('#modalPeminjaman').modal('show');
                    }
                });
            });

            // **HAPUS Peminjaman DENGAN KONFIRMASI SWEETALERT**
            $('#PeminjamanTable').on('click', '.btnDelete', function() {
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
                            url: `/api/peminjaman/${id}`,
                            type: 'DELETE',
                            headers: {
                                'Authorization': 'Bearer ' + token
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Peminjaman berhasil dihapus!',
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

            // **PENGEMBALIAN SWEETALERT**
            $('#PeminjamanTable').on('click', '.btnKembali', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Mengembalikan Buku!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Kembalikan!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/peminjaman/pengembalian/${id}`,
                            type: 'GET',
                            headers: {
                                'Authorization': 'Bearer ' + token
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Buku Berhasil Di Kembalikan!',
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
                                    text: 'Terjadi kesalahan saat Mengembalikan Buku!',
                                });
                            }
                        });
                    }
                });
            });

            $('#user_id').select2({
                dropdownParent: $('#modalPeminjaman'),
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return "Ketika Yang Anda Cari !"; // Ubah teks default
                    }
                },
                placeholder: 'Pilih Peminjaman',
                allowClear: true,
                ajax: {
                    url: '/api/users',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250, // Menunda request untuk menghindari terlalu banyak permintaan
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: function(params) {
                        return {
                            search: params.term, // Mengirim parameter pencarian ke API
                            page: params.page || 1 // Jika API mendukung pagination
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(user => ({
                                id: user.id,
                                text: user.nama
                            }))
                        };
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    },
                    cache: true
                },
                minimumInputLength: 1 // Menunggu pengguna mengetik sebelum memuat data
            }).next('.select2-container').addClass('form-select w-100');

            $('label[for="user_id"]').addClass('form-label');


            $('#buku_id').select2({
                dropdownParent: $('#modalPeminjaman'),
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return "Ketika Yang Anda Cari !"; // Ubah teks default
                    }
                },
                placeholder: 'Pilih Buku',
                allowClear: true,
                ajax: {
                    url: '/api/buku',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250, // Hindari terlalu banyak request dalam waktu singkat
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: function(params) {
                        return {
                            search: params.term, // Mengirim teks pencarian ke API
                            page: params.page || 1 // Jika API mendukung pagination
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(buku => ({
                                id: buku.id,
                                text: `${buku.judul} - Pengarang: ${buku.pengarang}`
                            }))
                        };
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    },
                    cache: true
                },
                minimumInputLength: 1 // Harus mengetik minimal 1 karakter sebelum request API
            }).next('.select2-container').addClass('form-select w-100');

            $('label[for="buku_id"]').addClass('form-label');
        });
    </script>
@endsection
