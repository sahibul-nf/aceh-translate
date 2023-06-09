@extends('home.master')

@section('header-menu-list')
    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" style="cursor: pointer;" href="{{ route('home.translate') }}">Translate</a></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-7 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td style="font-size: 1.3em; font-weight: bold;">Sawah</td>
                        </tr>
                        <tr>
                            <td>
                                @php
                                    // Untuk menghindari kata yang duplikat, jadi saya buat variable jejak. Kemudian nanti dicek didalam foreach apakah hasil yg diiterasi sudah pernah ditampilkan sebelumnya.
                                    $lastWord = null;
                                @endphp
                                <table class="table table-bordered table-striped" id="daftar-kosakata">
                                    <thead>
                                        <tr>
                                            <th>Kosakata</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['dictionaries']->sortBy('aceh') as $dictionary)
                                            @if ($lastWord != $dictionary->aceh)
                                                <tr>
                                                    <td style="width: 70%">
                                                        {{ ucfirst($dictionary->aceh) }}
                                                    </td>
                                                    <td class="text-center align-middle pt-3">
                                                        <a href="{{ route('home.kamus', ['word' => $dictionary->aceh]) }}" class="text-center btn {{ isset($data['word']) ? ($data['word'] == $dictionary->aceh ? 'btn-secondary' : 'btn-outline-secondary') : 'btn-outline-secondary' }} mr-2 mb-2">Lihat Detail</a>
                                                    </td>
                                                </tr>
                                            @endif
                                            @php
                                                $lastWord = $dictionary->aceh;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-sm-12">
            <div class="card">
                <div class="card-body">
                    @if (isset($data['word']))
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td colspan="2" style="font-size: 1.3em; font-weight: bold;">Detail</td>
                            </tr>
                            <tr>
                                <td style="width: 38%">Kosakata (Aceh)</td>
                                <td>{{ ucfirst($data['word']) }}</td>
                            </tr>
                            <tr>
                                <td style="width: 38%">Kosakata (Indonesia)</td>
                                <td>
                                    @if (is_array($data['translatedWord']))
                                        {{ ucfirst(join(', ', $data['translatedWord'])) }}
                                    @else
                                        {{ ucfirst($data['translatedWord']) }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 38%">Deskripsi</td>
                                <td>
                                    @if (isset($data['description']))
                                        @if (is_array($data['description']))
                                            {!! $data['description'][0] !!}
                                        @else
                                            {!! $data['description'] !!}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 38%">Gambar</td>
                                <td>
                                    @if (isset($data['imagePreview']))
                                        @if (is_array($data['imagePreview']))
                                            @foreach ($data['imagePreview'] as $img)
                                                <img src="{{ asset('assets/img/translate-images/' . $img) }}" alt="Preview" style="width: 220px; margin: 0 0.5em 0.5em 0;">
                                            @endforeach
                                        @else
                                            <img src="{{ asset('assets/img/translate-images/' . $data['imagePreview']) }}" alt="Preview" style="width: 220px; margin: 0 0.5em 0.5em 0;">
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 38%">Audio</td>
                                <td>
                                    @if (isset($data['audio']))
                                        @if (is_array($data['audio']))
                                            @foreach ($data['audio'] as $aud)
                                                <audio controls preload="metadata" style=" width:300px;">
                                                    <source src="{{ asset('assets/audio/translate-audio/'.$aud) }}" type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            @endforeach
                                        @else
                                            <audio controls preload="metadata" style=" width:300px;">
                                                <source src="{{ asset('assets/audio/translate-audio/'.$data['audio']) }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    @else
                        <div class="text-center" style="padding: 9.4em 0;">
                            <i>Klik kosakata untuk menampilkan detail</i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        $(document).ready( function () {
            $('#daftar-kosakata').DataTable(
                {
                    responsive: true,
                    lengthMenu: [10, 25, 50, 100, 1000],
                }
            );
        } );
    </script>
@endsection
