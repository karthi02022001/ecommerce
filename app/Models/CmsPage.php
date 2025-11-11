<?php
// app/Models/CmsPage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = ['slug', 'status'];

    public function translations()
    {
        return $this->hasMany(CmsPageTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    // Get translated title
    public function getTitle($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->title : $this->slug;
    }

    // Get translated content
    public function getContent($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->content : '';
    }
}
