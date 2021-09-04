<?php
use Illuminate\View\Factory;

/**
 * @var $exception \Symfony\Component\HttpKernel\Exception\HttpException
 * @var $__env Factory
 */

$__env->startSection('title', $exception->getMessage());
$__env->startSection('code', '503');
$__env->startSection('message', $exception->getMessage());

echo $__env->make('errors::minimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render();
?>
