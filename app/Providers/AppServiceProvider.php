<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Policies\BlogPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }

    /**
     * Register application policies.
     */
    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::policy(Blog::class, BlogPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(BlogComment::class, BlogPolicy::class);
    }
}
