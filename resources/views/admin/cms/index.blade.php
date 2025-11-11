{{-- resources/views/admin/cms/index.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">{{ __('CMS Pages') }}</h2>
        </div>
        <div class="col-md-6 text-end">
            @if(auth('admin')->user()->hasPermission('cms.create'))
                <a href="{{ route('admin.cms.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> {{ __('Create CMS Page') }}
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Slug') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cmsPages as $page)
                            <tr>
                                <td>{{ $loop->iteration + ($cmsPages->currentPage() - 1) * $cmsPages->perPage() }}</td>
                                <td>{{ $page->getTitle() }}</td>
                                <td>
                                    <code>{{ $page->slug }}</code>
                                </td>
                                <td>
                                    @if(auth('admin')->user()->hasPermission('cms.edit'))
                                        <form action="{{ route('admin.cms.toggle-status', $page->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $page->status === 'active' ? 'success' : 'danger' }}">
                                                {{ $page->status === 'active' ? __('Active') : __('Inactive') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-{{ $page->status === 'active' ? 'success' : 'danger' }}">
                                            {{ $page->status === 'active' ? __('Active') : __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cms.page', $page->slug) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="bi bi-eye"></i> {{ __('View') }}
                                    </a>

                                    @if(auth('admin')->user()->hasPermission('cms.edit'))
                                        <a href="{{ route('admin.cms.edit', $page->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i> {{ __('Edit') }}
                                        </a>
                                    @endif

                                   {{--  @if(auth('admin')->user()->hasPermission('cms.delete'))
                                        <form action="{{ route('admin.cms.destroy', $page->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                <i class="bi bi-trash"></i> {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @endif --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-muted mb-0">{{ __('No CMS pages found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cmsPages->hasPages())
                <div class="mt-4">
                    {{ $cmsPages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection