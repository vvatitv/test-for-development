@extends('layouts.app')
@section('content')
    <steps-ratings-index :step='@json($step)' :page="{{ $page }}" />
@endsection