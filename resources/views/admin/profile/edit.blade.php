@extends('layouts.admin')

@section('title', 'Edit Profil')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 gap-2">
        <i class="bi bi-pencil-square">
        </i>Edit Profil
    </h4>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="p-3 bg-white shadow rounded">
                @include('admin.profile.update-profile-information-form')
            </div>
        </div>

        <div class="col-md-6">
            <div class="p-3 bg-white shadow rounded">
                @include('admin.profile.update-password-form')
            </div>
        </div>

        <div class="col-12">
            <div class="p-3 bg-white shadow rounded">
                @include('admin.profile.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
