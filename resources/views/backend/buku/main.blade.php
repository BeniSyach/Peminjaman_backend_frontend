@extends('layouts.app')

@section('title', 'Buku')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Master Data /</span> Pengaturan Buku
        </h4>

        <div class="card">
            <h5 class="card-header d-flex justify-content-between">
                Data Buku
                <button class="btn btn-primary" id="btnTambah">Tambah Buku</button>
            </h5>
            <div class="table-responsive text-nowrap">
                <table class="table" id="BukuTable">
                    <thead>
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>foto</th>
                            <th>Kode Buku</th>
                            <th>Kategori</th>
                            <th>Pengarang</th>
                            <th>Judul</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Keluar</th>
                            <th>Sisa</th>
                            <th>Tanggal Penginputan</th>
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
    <div class="modal fade" id="modalBuku" tabindex="-1" aria-labelledby="modalBukuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBukuLabel">Tambah Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formBuku">
                        <input type="hidden" id="BukuId" name="id">

                        <div class="mb-3">
                            <label for="kode_buku" class="form-label">Kode Buku</label>
                            <input type="text" class="form-control" id="kode_buku" name="kode_buku" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" class="form-control" id="kategori" name="kategori" required>
                        </div>
                        <div class="mb-3">
                            <label for="pengarang" class="form-label">Pengarang</label>
                            <input type="text" class="form-control" id="pengarang" name="pengarang" required>
                        </div>
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="judul" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="penerbit" class="form-label">Penerbit</label>
                            <input type="text" class="form-control" id="penerbit" name="penerbit" required>
                        </div>
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="text" class="form-control" id="tahun" name="tahun" required>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="text" class="form-control" id="jumlah" name="jumlah" required>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" required>
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

            // Jika token tidak ada, redirect ke halaman login
            if (!token) {
                window.location.href = '/';
                return;
            }

            var table = $('#BukuTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/api/buku',
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
                        data: 'gambar',
                        render: function(data) {
                            if (!data) {
                                return 'Tidak ada gambar';
                            }
                            const gambar =
                                `/storage/${data}`; // Pastikan path benar sesuai struktur file
                            return `<img src="${gambar}" alt="Gambar Buku" width="50" height="50">`;
                        }
                    },
                    {
                        data: 'kode_buku'
                    },
                    {
                        data: 'kategori'
                    },
                    {
                        data: 'pengarang'
                    },
                    {
                        data: 'judul'
                    },
                    {
                        data: 'penerbit'
                    },
                    {
                        data: 'tahun'
                    },
                    {
                        data: 'jumlah'
                    },
                    {
                        data: 'keluar'
                    },
                    {
                        data: 'sisa'
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

            // **TOMBOL TAMBAH Buku**
            $('#btnTambah').click(function() {
                $('#formBuku')[0].reset();
                $('#BukuId').val('');
                $('#modalBukuLabel').text('Tambah Buku');
                $('#modalBuku').modal('show');
            });

            // **SUBMIT FORM (TAMBAH/EDIT)**
            $('#formBuku').submit(function(e) {
                e.preventDefault();
                const id = $('#BukuId').val();
                const method = 'POST';
                const url = id ? `/api/buku/${id}?_method=patch` : '/api/buku';
                let formData = new FormData(this);

                $.ajax({
                    url: url,
                    type: method,
                    processData: false, // Jangan diproses sebagai string
                    contentType: false, // Jan
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: formData,
                    success: function(response) {
                        $('#modalBuku').modal('hide');
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
                        $('#modalBuku').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menyimpan data!',
                        });
                    }
                });
            });

            // **EDIT Buku**
            $('#BukuTable').on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `/api/buku/${id}`,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        console.log('data buku', response);
                        $('#BukuId').val(response.id);
                        $('#kode_buku').val(response.kode_buku);
                        $('#kategori').val(response.kategori);
                        $('#pengarang').val(response.pengarang);
                        $('#judul').val(response.judul);
                        $('#penerbit').val(response.penerbit);
                        $('#tahun').val(response.tahun);
                        $('#jumlah').val(response.jumlah);
                        $('#keluar').val(response.keluar);
                        $('#sisa').val(response.sisa);
                        // hapus required
                        $('#gambar').removeAttr('required');
                        $('#modalBukuLabel').text('Edit Buku');
                        $('#modalBuku').modal('show');
                    }
                });
            });

            // **HAPUS Buku DENGAN KONFIRMASI SWEETALERT**
            $('#BukuTable').on('click', '.btnDelete', function() {
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
                            url: `/api/buku/${id}`,
                            type: 'DELETE',
                            headers: {
                                'Authorization': 'Bearer ' + token
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Buku berhasil dihapus!',
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
