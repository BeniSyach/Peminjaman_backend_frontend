@extends('layouts.auth')

@section('auth-content')
    {{-- content --}}
    <div class="card">
        <div class="card-body">
            {{-- Logo --}}
            @include('auth.partials.logo')

            <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('post.login') }}">
                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukkan NIK"
                        autofocus required pattern="[0-9]+" />
                    <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                </div>
                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password</label>

                    </div>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password" />
                        <span class="cursor-pointer input-group-text"><i class="bx bx-hide"></i></span>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember-me" />
                        <label class="form-check-label" for="remember-me"> Remember Me </label>
                    </div>
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                </div>

            </form>

            {{-- <div class="mb-3">
                <div class="row">
                    <div class="col-md">
                        <a class="btn btn-outline-dark w-100 star-bg" href="#">
                            <img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in"
                                src="{{ asset('assets/img/icons/google-96.png') }}" />
                            Signin with Google
                        </a>
                    </div>
                </div>
            </div> --}}

        </div>


    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const user = localStorage.getItem('user');
            const token = localStorage.getItem('token');

            if (user && token) {
                window.location.href = '/home';
            }
        });

        document.getElementById('nik').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, ''); // Hanya angka
        });
        $(document).ready(function() {
            $('#formAuthentication').submit(function(event) {
                event.preventDefault(); // Mencegah form submit default

                let formData = {
                    nik: $('#nik').val(),
                    password: $('#password').val(),
                    _token: $('input[name="_token"]').val() // Token CSRF Laravel
                };

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(response) {

                        if (response.user && response.user.role === 'customer') {
                            Swal.fire({
                                title: 'Akses Ditolak',
                                text: 'Hanya admin dan petugas yang bisa login!',
                                icon: 'warning',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            return;
                        }

                        localStorage.setItem('user', JSON.stringify(response.user));
                        localStorage.setItem('token', response.token);

                        Swal.fire({
                            title: 'Success!',
                            text: 'Login successful. Redirecting...',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || '/home';
                            // window.location.href = response.redirect || '/check-auth';
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Login failed. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
@endsection
