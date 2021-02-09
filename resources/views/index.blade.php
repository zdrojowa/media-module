@extends('DashboardModule::dashboard.index', ['title' => 'Media'])

@section('navbar-actions')
    <b-nav-form>
        <b-form-input size="sm" class="mr-sm-2" placeholder="Szukaj" v-model="search"></b-form-input>
        <b-button size="sm" class="my-2 my-sm-0" type="button" @click="find">Szukaj</b-button>
    </b-nav-form>
@endsection

@section('content')
    <b-container fluid>
        <index
            upload-route="{{ route('MediaModule::media.upload.ajax') }}"
            media-route="/media/"
            info-route="/dashboard/media/id/info"
            editor-route="{{ route('MediaModule::media.image.editor.save') }}"
            delete-route="/api/media/delete/"
            @upload="upload"
            @remove="remove"
            :files="files"
        >
        </index>
    </b-container>
@endsection

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="{{ mix('vendor/css/MediaModule.css') }}">
@endsection

@section('javascripts')
    <script src="{{ mix("vendor/js/MediaModule.js") }}"></script>
@endsection
