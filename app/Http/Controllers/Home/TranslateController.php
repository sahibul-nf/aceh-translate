<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Atomescrochus\StringSimilarities\Compare;
use App\Models\Dictionary; // Memanggil model Dictionary

use App\Models\VocabularyRequest; // Memanggil model VocabularyRequest
use Illuminate\Http\Request; // Manggil package untuk mengelola request
use App\Models\VocabularySuggestion; // Memanggil model VocabularySuggestion
use Illuminate\Support\Facades\Validator; // Manggil package untuk mengelola validator

class TranslateController extends Controller
{
    // Fungsi untuk menampilkan halaman translate dan juga untuk menampilkan detail dari kosakata yg kita translatekan
    public function index($word = null, $translateTo = null)
    {
        $data = ['title' => 'Kamus Bahasa Aceh | Translate', 'headerTitle' => 'Translate', 'headerLink' => route('home.translate')];

        if ($word != null && $translateTo != null) {
            if ($translateTo == 'aceh' || $translateTo == 'indonesia') {

                $data['word'] = html_entity_decode($word);

                if ($translateTo == 'aceh') {
                    $data['translateFrom'] = 'indonesia';
                    $data['translateTo'] = 'aceh';
                } else if ($translateTo == 'indonesia') {
                    $data['translateFrom'] = 'aceh';
                    $data['translateTo'] = 'indonesia';
                }

                $translateFrom = $data['translateFrom'];
                $translateTo = $data['translateTo'];

                $query = Dictionary::where($translateFrom, html_entity_decode($word));

                if ($query->exists()) {

                    if ($query->get()->count() > 1) {

                        $translatedWord = [];
                        $description = [];
                        $imagePreview = [];
                        $audio = [];

                        foreach ($query->get() as $row) {
                            array_push($translatedWord, $row->$translateTo);
                            if ($row->deskripsi != null) {
                                array_push($description, $row->deskripsi);
                            }
                            if ($row->gambar != null) {
                                array_push($imagePreview, $row->gambar);
                            }
                            if ($row->audio != null) {
                                array_push($imagePreview, $row->audio);
                            }
                        }

                        if (sizeof($description) == 0) {
                            $description = null;
                        }
                        if (sizeof($imagePreview) == 0) {
                            $imagePreview = null;
                        }
                        if (sizeof($audio) == 0) {
                            $audio = null;
                        }

                        $data['translatedWord'] = $translatedWord;
                        $data['description'] = $description;
                        $data['imagePreview'] = $imagePreview;
                        $data['audio'] = $audio;
                    } else {
                        $data['translatedWord'] = $query->first()->$translateTo;
                        $data['description'] = $query->first()->deskripsi;
                        $data['imagePreview'] = $query->first()->gambar;
                        $data['audio'] = $query->first()->audio;
                    }
                } else {
                    $data['translatedWord'] = null;
                    $data['description'] = null;
                    $data['imagePreview'] = null;
                    $data['audio'] = null;

                    $wordInput = html_entity_decode($word);

                    $recommendationList = DictionarySimiliarity::getRecommendationList($wordInput, $translateFrom);

                    return view('home.translate', ['data' => $data, 'list' => $recommendationList]);
                }
            } else {
                return abort(404);
            }
        }

        return view('home.translate', ['data' => $data]);
    }

    // Fungsi untuk mengirim saran terjemahan
    public function pushSuggestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aceh' => 'required',
            'indonesia' => 'required'
        ], [
            'required' => 'Harap masukkan :attribute'
        ], [
            'aceh' => 'Kosakata Bahasa Aceh',
            'indonesia' => 'Kosakata Bahasa Indonesia'
        ]);

        if ($validator->fails()) {
            return redirect()->route('home.translate')
                ->withErrors($validator, 'vocabularySuggestion')
                ->withInput();
        }

        $data = [
            'aceh' => $request->aceh,
            'indonesia' => $request->indonesia,
        ];

        // Menambah Deskripsi
        if (!empty($request->deskripsi)) {
            $data['deskripsi'] = $request->deskripsi;
        }

        VocabularySuggestion::create($data);

        return redirect()->route('home.translate')->with('success', 'Berhasil menyarankan terjemahan');
    }

    // Fungsi untuk mengirim request terjemahan
    public function pushRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kosakata' => 'required',
            'bahasa_tujuan' => 'required'
        ], [
            'required' => 'Harap masukkan :attribute'
        ], [
            'kosakata' => 'Kosakata',
            'bahasa_tujuan' => 'Bahasa Tujuan'
        ]);

        if ($validator->fails()) {
            return redirect()->route('home.translate')
                ->withErrors($validator, 'vocabularyRequest')
                ->withInput();
        }

        VocabularyRequest::create([
            'kosakata' => $request->kosakata,
            'bahasa_tujuan' => $request->bahasa_tujuan
        ]);

        return redirect()->route('home.translate')->with('success', 'Berhasil request terjemahan');
    }

    // Fungsi untuk menampilkan rekomendasi kata pencarian
    public function getRecommendationList()
    {

        $dictionaries = array();

        // load all data from db
        $allData = Dictionary::all();

        $similiarity = 0;

        foreach ($allData as $key => $value) {
            $comparison = new \Atomescrochus\StringSimilarities\Compare();
            $similiarity = $comparison->jaroWinkler('kuéh', $value->aceh);

            if ($similiarity >= 0.75) {
                $dictionary = new DictionarySimiliarity(
                    $value->aceh,
                    $value->indonesia,
                    $value->inggris,
                    $similiarity,
                );

                array_push($dictionaries, $dictionary);
            }
        }

        $order = array();

        for ($i = 0; $i < count($dictionaries); $i++) {
            array_push($order, $dictionaries[$i]->similiarity);
        }

        for ($i = 0; $i < count($dictionaries); $i++) {
            usort($dictionaries, function ($a, $b) use ($order) {
                return $a->similiarity < $b->similiarity;
            });
        }

        $finalResult = array();

        if (count($dictionaries) > 5) {
            for ($i = 0; $i < count($dictionaries); $i++) {
                if ($i < 5) {
                    array_push($finalResult, $dictionaries[$i]);
                }
            }
        }

        // echo json_encode($finalResult);
        return view('home.recommendation-list', ['list' => $finalResult]);
    }
}

class DictionarySimiliarity
{
    public $aceh;
    public $indonesia;
    public $inggris;
    public $similiarity;

    function __construct($aceh = null, $indonesia = null, $inggris = null, $similiarity = null)
    {
        $this->aceh = $aceh;
        $this->indonesia = $indonesia;
        $this->inggris = $inggris;
        $this->similiarity = $similiarity;
    }

    public static function getRecommendationList(string $word, string $language)
    {
        $dictionaries = array();

        // load all data from db
        $allData = Dictionary::all();

        $similiarity = 0;

        
        foreach ($allData as $key => $value) {
            $word2 = '';
            if ($language == 'aceh') {
                $word2 = $value->aceh;
            } elseif ($language == 'indonesia') {
                $word2 = $value->indonesia;
            } else {
                $word2 = $value->inggris;
            }

            $comparison = new \Atomescrochus\StringSimilarities\Compare();
            $similiarity = $comparison->jaroWinkler($word, $word2);

            if ($similiarity >= 0.75) {
                $dictionary = new DictionarySimiliarity(
                    $value->aceh,
                    $value->indonesia,
                    $value->inggris,
                    $similiarity,
                );

                array_push($dictionaries, $dictionary);
            }
        }

        $order = array();

        for ($i = 0; $i < count($dictionaries); $i++) {
            array_push($order, $dictionaries[$i]->similiarity);
        }

        for ($i = 0; $i < count($dictionaries); $i++) {
            usort($dictionaries, function ($a, $b) use ($order) {
                return $a->similiarity < $b->similiarity;
            });
        }

        $finalResult = array();

        if (count($dictionaries) > 5) {
            for ($i = 0; $i < count($dictionaries); $i++) {
                if ($i < 5) {
                    array_push($finalResult, $dictionaries[$i]);
                }
            }
        }

        // echo json_encode($finalResult);
        return $finalResult;
    }
}
