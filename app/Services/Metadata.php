<?php

namespace App\Services;

use App\Models\Article;

// Based on martinbean's https://laracasts.com/discuss/channels/laravel/correct-way-to-include-seo-tags-in-laravel-blade?page=1&replyId=954916
class Metadata
{
    protected static $data = [];

    public static function set(string $key, string $value): void
    {
        static::$data[$key] = $value;
    }

    public static function get(string $key, string $default = null): ?string
    {
        return static::$data[$key] ?? $default;
    }

    public static function all(): array
    {
        return static::$data;
    }

    public static function getMetaTags(): array
    {
        return array_filter(static::$data, function ($key) {
            return !str_starts_with($key, 'og:');
        }, ARRAY_FILTER_USE_KEY);
    }

    public static function getOpenGraphMetaTags(): array
    {
        return array_filter(static::$data, function ($key) {
            return str_starts_with($key, 'og:');
        }, ARRAY_FILTER_USE_KEY);
    }


    public static function fromArticle(Article $article) {
        Metadata::set('title', $article->meta_title ?? $article->title);
        Metadata::set('description', $article->meta_description ?? substr($article->content, 0, 150));
        Metadata::set('keywords', $article->meta_keywords ?? '');
        Metadata::set('og_type', 'article');
        Metadata::set('og_title', $article->meta_title ?? $article->title);
        Metadata::set('og_description', $article->meta_description ?? substr($article->content, 0, 150));
        Metadata::set('og_image', $article->meta_image ?? '');
        Metadata::set('twitter_card', 'summary_large_image');
        Metadata::set('twitter_title', $article->meta_title ?? $article->title);
        Metadata::set('twitter_description', $article->meta_description ?? substr($article->content, 0, 150));
        Metadata::set('twitter_image', $article->meta_image ?? '');
    }
}
