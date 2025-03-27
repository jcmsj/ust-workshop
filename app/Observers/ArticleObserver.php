<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleObserver
{
    public function saving(Article $article): void
    {
        // Generate meta title if not set
        if (empty($article->meta_title)) {
            $article->meta_title = $article->title;
        }

        // Generate meta description from summary or truncated body
        if (empty($article->meta_description)) {
            $article->meta_description = $article->summary ?? 
                Str::limit(strip_tags($article->body), 160);
        }

        // Generate keywords from title and category
        if (empty($article->meta_keywords)) {
            $keywords = explode(' ', $article->title);
            if ($article->category) {
                $keywords[] = $article->category->name;
            }
            $article->meta_keywords = implode(', ', array_unique($keywords));
        }
    }
}
