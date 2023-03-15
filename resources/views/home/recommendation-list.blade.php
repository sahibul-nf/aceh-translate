<!DOCTYPE html>
<html>

<head>
    <title>Laravel Tailwind CSS Modal Example - NiceSnippets.com</title>
    <meta charset="UTF-8" />
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}">

</head>

<body>
    {{-- Recommendation List View --}}
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalToggleLabel">Modal 1</h5> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-10">
                    <ion-icon name="alert-circle-outline"></ion-icon>
                    <h1 class="fs-4">Opps</h1>
                    <p class="mt-8 mb-8">Terjemahan tidak ditemukan. <br>Mungkin kata yang kamu maksud seperti dibawah
                        ini.
                    </p>

                    <div class="btn-group-vertical btn-group-lg w-full" role="group" aria-label="...">
                        @foreach ($list as $item)
                            <button type="button" class="btn btn-outline-secondary">
                                {{ $item->aceh }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer items-center justify-center content-center">
                    <button class="btn btn-primary w-full h-12 px-10" data-bs-target="#exampleModalToggle2"
                        data-bs-toggle="modal" data-bs-dismiss="modal">Terjemah kata lain</button>
                </div>
            </div>
        </div>
    </div>

    <a class="btn btn-primary" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Open first modal</a>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.openModal').on('click', function(e) {
                $('#interestModal').removeClass('invisible');
            });
            $('.closeModal').on('click', function(e) {
                $('#interestModal').addClass('invisible');
            });
        });
    </script>
    {{-- Icons --}}
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- SweetAlert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</body>

</html>
