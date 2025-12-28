<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\Category;

class QuestController extends Controller
{
    public function index()
    {
        $seo_description = "SI-GoChild adalah platform sistem pakar digital yang dirancang untuk memantau tumbuh kembang anak secara presisi. Menggunakan standar MMDST (Denver II) dan monitoring antropometri untuk mendeteksi dini gangguan perkembangan serta menyediakan laporan harian daycare yang informatif.";
        $seo_meta_title  = "SI-GoChild - Sistem Pakar & Monitoring Tumbuh Kembang Anak";
        $seo_title = "SI-GoChild | Solusi Cerdas Pantau Tumbuh Kembang Si Kecil";
        $seo_key = 'SI-GoChild, si-gochild, sistem informasi anak, sistem pakar tumbuh kembang, MMDST, Denver II, skrining anak, deteksi dini perkembangan anak, monitoring gizi anak, antropometri anak, laporan harian daycare, aplikasi kesehatan anak, perkembangan motorik anak, stimulasi anak usia dini, dashboard kesehatan anak, rekam medis digital anak, KMS digital, sistem informasi daycare';

        return view('index', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    }

    public function about()
    {
        $seo_description = "Pelajari misi SI-GoChild dalam membantu orang tua dan tenaga pengajar melakukan deteksi dini tumbuh kembang anak. Kami menggabungkan metodologi sistem pakar berbasis MMDST dengan teknologi informasi untuk menciptakan generasi yang sehat, cerdas, dan terpantau secara berkala.";
        $seo_meta_title = "Tentang SI-GoChild - Inovasi Monitoring Perkembangan Anak";
        $seo_title = "Mengenal SI-GoChild - Sistem Deteksi Dini & Monitoring";
        $seo_key = 'apa itu si-gochild, sistem pakar denver ii, metodologi mmdst, deteksi dini anak, profil si-gochild, keunggulan si-gochild, monitoring perkembangan digital, pengembang si-gochild, sistem informasi pendidikan anak usia dini, aplikasi skrining tumbuh kembang';

        return view('about', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    }

    public function services()
    {
        $seo_description = "Jelajahi fitur unggulan SI-GoChild: Skrining Perkembangan Denver II (MMDST), Laporan Pertumbuhan Antropometri Otomatis, Laporan Aktivitas Harian Digital, dan Wawasan Kesehatan dari para pakar tumbuh kembang anak.";
        $seo_meta_title = "Layanan & Fitur Unggulan - SI-GoChild";
        $seo_title = "Fitur Sistem SI-GoChild | Skrining & Monitoring Digital";
        $seo_key = 'layanan skrining mmdst, fitur antropometri anak, laporan harian digital, konsultasi tumbuh kembang, grafik pertumbuhan anak, kms digital, evaluasi motorik anak, skrining bahasa anak, monitoring personal sosial anak, sistem pakar daycare';

        return view('service', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    }

    public function blogs(Request $request)
    {
        $seo_description = "Pusat wawasan SI-GoChild. Temukan berbagai artikel edukatif, panduan pola asuh, tips kesehatan, dan informasi terkini seputar stimulasi tumbuh kembang anak yang ditulis oleh praktisi dan pakar.";
        $seo_meta_title = "Wawasan & Artikel Tumbuh Kembang - SI-GoChild";
        $seo_title = "Blog Edukasi SI-GoChild | Informasi Kesehatan & Pola Asuh";
        $seo_key = 'blog kesehatan anak, tips pola asuh, artikel tumbuh kembang, panduan stimulasi anak, edukasi orang tua, info gizi anak, kegiatan kreatif anak, blog si-gochild, berita perkembangan anak, tips daycare';

        // Menampilkan kategori untuk filter
        $categories = Category::all();

        // Logika pencarian dan filter kategori
        $query = Article::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Mengambil artikel dengan kategori, paginasi 9 item per halaman
        $articles = $query->with('category')->latest()->paginate(9)->appends([
            'search' => $request->search,
            'category' => $request->category
        ]);

        return view('blogs', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key', 'articles', 'categories'));
    }

    public function blogsShow($slug)
    {
        // Mencari artikel berdasarkan slug
        $article = Article::where('slug', $slug)->firstOrFail();

        // SEO dinamis berdasarkan judul dan isi artikel
        $seo_description = \Illuminate\Support\Str::limit(strip_tags($article->content), 160);
        $seo_meta_title = $article->title . " - SI-GoChild";
        $seo_title = $article->title . " | Artikel SI-GoChild";
        $seo_key = $article->category->category_name . ", " . $article->title . ", artikel si-gochild, informasi tumbuh kembang";

        // Load relasi kategori dan penulis
        $article->load('category', 'user');

        // Mengambil artikel terkait dalam kategori yang sama (kecuali artikel ini sendiri)
        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(3)
            ->get();

        return view('blogs.blogs-show.index', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key', 'article', 'relatedArticles'));
    }
}
