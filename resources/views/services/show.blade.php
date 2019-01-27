@extends('layouts/app')

<?php
/**
 * @var \App\Entity\Service $service
 */
?>

@section('content')
    {{ $service->getName() }}
@endsection
