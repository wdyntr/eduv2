@extends('user.layouts.base')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

@if($sections['admin']) @include('user.dashboard._admin') @endif
@if($sections['reviewer']) @include('user.dashboard._reviewer') @endif
@if($sections['penulis']) @include('user.dashboard._penulis') @endif
@if($sections['sekolah']) @include('user.dashboard._sekolah') @endif
@if($sections['operator']) @include('user.dashboard._operator') @endif

@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    @if($sections['admin']) loadDashboard(); @endif
    @if($sections['reviewer']) loadDashboardReviewer(); @endif
    @if($sections['operator']) loadDashboardOperator(); @endif
  });
</script>
@endsection