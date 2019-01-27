@extends('layouts/app')

<?php
/**
 * @var \App\Entity\Service[] $services
 */
?>

@section('content')
    @forelse($services as $service)
        <span>{{$service->getName()}}</span>
    @empty
        No services have been created
    @endforelse
@endsection
