@extends('template.profile')
@section('title'){{ $content->title ?? '' }}@stop
@section('content')
        {!! $content->content ?? '' !!}
        <dyntreecomponent
            :referalsgl="{{ $referals }}"
        ></dyntreecomponent>
@stop
