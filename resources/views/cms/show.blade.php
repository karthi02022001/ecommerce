{{-- resources/views/site/cms/show.blade.php --}}
@extends('layouts.app')

@section('title', $metaTitle)
@section('meta_description', $metaDescription)

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="mb-4">{{ $title }}</h1>
                
                <div class="cms-content">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection