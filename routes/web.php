<?php

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\TeamInvitationController;
use App\Services\Metadata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Add this after the home route
Route::get('/articles', function () {
    $featuredCategory = ArticleCategory::where('name', 'featured')->firstOrFail();
    $featuredCarousel = $featuredCategory->articles()
        ->where('publish_status', 'published')
        ->orderBy('display_order')
        ->take(5)
        ->get();
    
    $articles = $featuredCategory->articles()
        ->with('author')
        ->where('publish_status', 'published')
        ->orderBy('display_order')
        ->paginate(9);
    
    return view('components.article-index', [
        'featuredCarousel' => $featuredCarousel,
        'articles' => $articles,
    ]);
})->name('articles.index');

// Main category pages
Route::get('/about', function () {
    $article = Article::where('slug', 'about')->firstOrFail();
    Metadata::fromArticle($article);
    return view('components.article-show', [
        'article' => $article,
    ]);
})->name('about');

// Regular articles show
Route::get('/articles/{article:slug}', function (Article $article) {
    Metadata::fromArticle($article);
    // paginate by 8
    $featured = ArticleCategory::with(['articles' => function ($query) {
        $query->where('publish_status', 'published')
              ->orderBy('display_order')
              ->paginate(8);
    }])->where('name', 'featured')->firstOrFail()->articles;
    
    return view('components.article-show', [
        'article' => $article,
        'featured' => $featured,
    ]);
})->name('articles.show');

Route::redirect('/login', '/app/login')->name('login');

Route::redirect('/register', '/app/register')->name('register');

Route::redirect('/dashboard', '/app')->name('dashboard');

Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
    ->middleware([
        'signed',
        'verified',
        'auth',
        AuthenticateSession::class
    ])->name('team-invitations.accept');

if (config('app.env') === 'local') {
    Route::get('/test-email', [App\Http\Controllers\EmailController::class, 'test']);
    Route::get('/approval-email', function () {
        return view('mail.user-approved', [
            'user' => App\Models\User::find(1),
        ]);
    });
}

Route::mailPreview();
