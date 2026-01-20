<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Public/ProductIndex');
})->name('public.products.index');

Route::get('/product/{id}', function (int $id) {
    return Inertia::render('Public/ProductShow', [
        'productId' => $id,
    ]);
})->name('public.products.show');

Route::get('/admin/login', function () {
    return Inertia::render('Admin/Login');
})->name('admin.login');

Route::get('/admin/products', function () {
    return Inertia::render('Admin/ProductIndex');
})->name('admin.products.index');

Route::get('/admin/products/create', function () {
    return Inertia::render('Admin/ProductForm', [
        'mode' => 'create',
    ]);
})->name('admin.products.create');

Route::get('/admin/products/{id}/edit', function (int $id) {
    return Inertia::render('Admin/ProductForm', [
        'mode' => 'edit',
        'productId' => $id,
    ]);
})->name('admin.products.edit');
