@extends('layouts/app')

<?php
/**
 * @var \App\Entity\Service $service
 */
?>

@section('content')
    <h2>Edit service</h2>
    {{$service->getName()}}
@endsection

