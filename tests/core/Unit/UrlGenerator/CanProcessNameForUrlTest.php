<?php

uses(\Lunar\Tests\Core\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use Lunar\FieldTypes\Text;
use Lunar\Models\Brand;
use Lunar\Generators\UrlGenerator;

beforeEach(function () {
    $this->generator = new UrlGenerator();
});

test('creates url from name column', function () {
    $brand = Brand::create([
        'name' => 'Test Brand',
        'attribute_data' => [
            'type' => new Text('Some Type')
        ]
    ]);

    $this->generator->handle($brand);

    expect($brand->urls)
        ->toHaveCount(1)
        ->first()->slug->toBe('test-brand');
});

test('creates url from attribute data name', function () {
    $brand = Brand::create([
        'name' => null,
        'attribute_data' => [
            'name' => new Text('Attribute Brand Name'),
            'type' => new Text('Some Type')
        ]
    ]);

    $this->generator->handle($brand);

    expect($brand->urls)
        ->toHaveCount(1)
        ->first()->slug->toBe('attribute-brand-name');
});

test('prefers column name over attribute name', function () {
    $brand = Brand::create([
        'name' => 'Column Brand Name',
        'attribute_data' => [
            'name' => new Text('Attribute Brand Name'),
            'type' => new Text('Some Type')
        ]
    ]);

    $this->generator->handle($brand);

    expect($brand->urls)
        ->toHaveCount(1)
        ->first()->slug->toBe('column-brand-name');
});

test('does not create url when both names are missing', function () {
    $brand = Brand::create([
        'name' => null,
        'attribute_data' => [
            'type' => new Text('Some Type')
        ]
    ]);

    $this->generator->handle($brand);

    expect($brand->urls)->toHaveCount(0);
});

test('does not create duplicate urls for same brand', function () {
    $brand = Brand::create([
        'name' => 'Test Brand'
    ]);

    // Create initial URL
    $this->generator->handle($brand);

    // Try to create URL again
    $this->generator->handle($brand);

    expect($brand->urls)
        ->toHaveCount(1)
        ->first()->slug->toBe('test-brand');
});
