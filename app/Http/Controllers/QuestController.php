<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\Category;

class QuestController extends Controller
{
    public function index()
    {
        $seo_description = "Al Jannah Preschool and Day Care adalah lembaga pendidikan anak usia dini dengan visi menjadi pusat tumbuh kembang anak, mewujudkan generasi sehat jasmani, rohani, beriman, dan berkarakter unggul. Daycare Al-Jannah menawarkan layanan pendidikan anak, kegiatan menyenangkan, serta perawatan personal untuk setiap anak.";
        $seo_meta_title  = "Al Jannah - Preschool and Day Care";
        $seo_title = "Al Jannah - Preschool and Day Care";
        $seo_key = 'Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah, daycare al jannah, daycare preschool, daycare untuk anak, daycare untuk bayi, daycare untuk anak-anak, daycare untuk anak usia dini, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, day, al,Al Jannah Preschool, daycare anak, daycare bayi, PAUD Al Jannah, taman kanak-kanak, daycare untuk anak, daycare untuk bayi, daycare untuk anak usia dini, layanan daycare anak, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, perawatan anak, pendidikan anak usia dini, pendidikan anak, pengasuhan anak, daycare terpercaya, layanan pendidikan anak, daycare di Jakarta, daycare di Indonesia, daycare terbaik, daycare dengan fasilitas lengkap, pendidikan karakter anak, pembelajaran anak usia dini, daycare ramah anak, tempat penitipan anak, taman kanak-kanak terbaik, daycare Al Jannah, preschool Al Jannah, daycare untuk anak-anak Indonesia';

        return view('index', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    }

    public function about()
    {
        $seo_description = "Al Jannah Preschool and Day Care adalah lembaga pendidikan anak usia dini yang berfokus pada tumbuh kembang anak dengan pendekatan berbasis Islam, menawarkan layanan pendidikan yang menyeluruh, kasih sayang, serta penanaman nilai-nilai agama sejak dini.";
        $seo_meta_title = "Tentang Al Jannah - Preschool and Day Care";
        $seo_title = "Tentang Al Jannah - Preschool and Day Care";
        $seo_key = 'apa itu al jannah daycare, apa itu daycare ,Al Jannah, daycare, preschool, pendidikan anak, daycare Al Jannah, pendidikan usia dini, PAUD, daycare terbaik,Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah, daycare al jannah, daycare preschool, daycare untuk anak, daycare untuk bayi, daycare untuk anak-anak, daycare untuk anak usia dini, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, day, al,Al Jannah Preschool, daycare anak, daycare bayi, PAUD Al Jannah, taman kanak-kanak, daycare untuk anak, daycare untuk bayi, daycare untuk anak usia dini, layanan daycare anak, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, perawatan anak, pendidikan anak usia dini, pendidikan anak, pengasuhan anak, daycare terpercaya, layanan pendidikan anak, daycare di Jakarta, daycare di Indonesia, daycare terbaik, daycare dengan fasilitas lengkap, pendidikan karakter anak, pembelajaran anak usia dini, daycare ramah anak, tempat penitipan anak, taman kanak-kanak terbaik, daycare Al Jannah, preschool Al Jannah, daycare untuk anak-anak Indonesia, al jannah kedungwuni, daycare kedungwuni, daycare pekalongan, al jannah pekalongan';

        return view('about', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    }

    public function services()
    {
        $seo_description = "Al Jannah Preschool and Day Care menawarkan berbagai layanan edukasi dan perawatan anak yang meliputi daycare untuk bayi dan anak-anak, pijat bayi, spa khusus bayi, dan layanan konsultasi perkembangan anak. Kami berkomitmen untuk menyediakan lingkungan yang aman dan mendukung tumbuh kembang anak secara holistik.";
        $seo_meta_title = "Layanan Al Jannah - Preschool and Day Care";
        $seo_title = "Layanan Al Jannah - Preschool and Day Care";
        $seo_key = 'Al Jannah, daycare, preschool, layanan anak, pijat bayi, spa bayi, daycare terbaik, layanan konsultasi anak, skrining tumbuh kembang,Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah, daycare al jannah, daycare preschool, daycare untuk anak, daycare untuk bayi, daycare untuk anak-anak, daycare untuk anak usia dini, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, day, al,Al Jannah Preschool, daycare anak, daycare bayi, PAUD Al Jannah, taman kanak-kanak, daycare untuk anak, daycare untuk bayi, daycare untuk anak usia dini, layanan daycare anak, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, perawatan anak, pendidikan anak usia dini, pendidikan anak, pengasuhan anak, daycare terpercaya, layanan pendidikan anak, daycare di Jakarta, daycare di Indonesia, daycare terbaik, daycare dengan fasilitas lengkap, pendidikan karakter anak, pembelajaran anak usia dini, daycare ramah anak, tempat penitipan anak, taman kanak-kanak terbaik, daycare Al Jannah, preschool Al Jannah, daycare untuk anak-anak Indonesia,al jannah kedungwuni, daycare kedungwuni, daycare pekalongan, al jannah pekalongan';

        return view('service', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    }

    public function blogs(Request $request)
    {
        $seo_description = "Dapatkan informasi terbaru mengenai aktivitas dan acara yang berlangsung di Al Jannah Preschool and Day Care. Jelajahi posting blog kami yang menyajikan berbagai kegiatan edukatif, bermain di luar ruangan, seni & kerajinan kreatif, dan masih banyak lagi.";
        $seo_meta_title = "Blog Aktivitas Daycare - Al Jannah Preschool and Day Care";
        $seo_title = "Blog Aktivitas Daycare - Al Jannah Preschool and Day Care";
        $seo_key = 'Blog daycare, aktivitas daycare, pendidikan usia dini, bermain di luar ruangan, seni dan kerajinan untuk anak, pembelajaran preschool, blog Al Jannah daycare,Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah, daycare al jannah, daycare preschool, daycare untuk anak, daycare untuk bayi, daycare untuk anak-anak, daycare untuk anak usia dini, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, day, al,Al Jannah Preschool, daycare anak, daycare bayi, PAUD Al Jannah, taman kanak-kanak, daycare untuk anak, daycare untuk bayi, daycare untuk anak usia dini, layanan daycare anak, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, perawatan anak, pendidikan anak usia dini, pendidikan anak, pengasuhan anak, daycare terpercaya, layanan pendidikan anak, daycare di Jakarta, daycare di Indonesia, daycare terbaik, daycare dengan fasilitas lengkap, pendidikan karakter anak, pembelajaran anak usia dini, daycare ramah anak, tempat penitipan anak, taman kanak-kanak terbaik, daycare Al Jannah, preschool Al Jannah, daycare untuk anak-anak Indonesia,al jannah kedungwuni, daycare kedungwuni, daycare pekalongan, al jannah pekalongan';
        // Retrieve categories to populate the filter dropdown
        $categories = Category::all();

        // Handle search and filter by category
        $query = Article::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Retrieve articles based on the query with pagination
        $articles = $query->with('category')->latest()->paginate(9)->appends([
            'search' => $request->search,
            'category' => $request->category
        ]);


        return view('blogs', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key', 'articles', 'categories'));
    }

    // public function blogsShow()
    // {
    //     $seo_description = "Dapatkan informasi terbaru mengenai aktivitas dan acara yang berlangsung di Al Jannah Preschool and Day Care. Jelajahi posting blog kami yang menyajikan berbagai kegiatan edukatif, bermain di luar ruangan, seni & kerajinan kreatif, dan masih banyak lagi.";
    //     $seo_meta_title = "Blog Aktivitas Daycare - Al Jannah Preschool and Day Care";
    //     $seo_title = "Blog Aktivitas Daycare - Al Jannah Preschool and Day Care";
    //     $seo_key = 'Blog daycare, aktivitas daycare, pendidikan usia dini, bermain di luar ruangan, seni dan kerajinan untuk anak, pembelajaran preschool, blog Al Jannah daycare,Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah, daycare al jannah, daycare preschool, daycare untuk anak, daycare untuk bayi, daycare untuk anak-anak, daycare untuk anak usia dini, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, day, al,Al Jannah Preschool, daycare anak, daycare bayi, PAUD Al Jannah, taman kanak-kanak, daycare untuk anak, daycare untuk bayi, daycare untuk anak usia dini, layanan daycare anak, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, perawatan anak, pendidikan anak usia dini, pendidikan anak, pengasuhan anak, daycare terpercaya, layanan pendidikan anak, daycare di Jakarta, daycare di Indonesia, daycare terbaik, daycare dengan fasilitas lengkap, pendidikan karakter anak, pembelajaran anak usia dini, daycare ramah anak, tempat penitipan anak, taman kanak-kanak terbaik, daycare Al Jannah, preschool Al Jannah, daycare untuk anak-anak Indonesia,al jannah kedungwuni, daycare kedungwuni, daycare pekalongan, al jannah pekalongan';

    //     return view('blogs.blogs-show.index', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key'));
    // }

    public function blogsShow($slug)
    {

        $seo_description = "Dapatkan informasi terbaru mengenai aktivitas dan acara yang berlangsung di Al Jannah Preschool and Day Care. Jelajahi posting blog kami yang menyajikan berbagai kegiatan edukatif, bermain di luar ruangan, seni & kerajinan kreatif, dan masih banyak lagi.";
        $seo_meta_title = "Blog Aktivitas Daycare - Al Jannah Preschool and Day Care";
        $seo_title = "Blog Aktivitas Daycare - Al Jannah Preschool and Day Care";
        $seo_key = 'Blog daycare, aktivitas daycare, pendidikan usia dini, bermain di luar ruangan, seni dan kerajinan untuk anak, pembelajaran preschool, blog Al Jannah daycare,Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah, daycare al jannah, daycare preschool, daycare untuk anak, daycare untuk bayi, daycare untuk anak-anak, daycare untuk anak usia dini, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, day, al,Al Jannah Preschool, daycare anak, daycare bayi, PAUD Al Jannah, taman kanak-kanak, daycare untuk anak, daycare untuk bayi, daycare untuk anak usia dini, layanan daycare anak, daycare untuk anak usia 0-2 tahun, daycare untuk anak usia 2-5 tahun, daycare untuk anak usia 5-8 tahun, perawatan anak, pendidikan anak usia dini, pendidikan anak, pengasuhan anak, daycare terpercaya, layanan pendidikan anak, daycare di Jakarta, daycare di Indonesia, daycare terbaik, daycare dengan fasilitas lengkap, pendidikan karakter anak, pembelajaran anak usia dini, daycare ramah anak, tempat penitipan anak, taman kanak-kanak terbaik, daycare Al Jannah, preschool Al Jannah, daycare untuk anak-anak Indonesia,al jannah kedungwuni, daycare kedungwuni, daycare pekalongan, al jannah pekalongan';

        // Find the article by slug
        $article = Article::where('slug', $slug)->firstOrFail();

        // Load the article with its category and user (author) details
        $article->load('category', 'user');

        // Fetch related articles based on the same category
        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(3)
            ->get();

        // Return the view with the article and related articles
        return view('blogs.blogs-show.index', compact('seo_description', 'seo_meta_title', 'seo_title', 'seo_key', 'article', 'relatedArticles'));
    }
}
