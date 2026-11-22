<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('nasabah can upload a document', function () {
    Storage::fake('local');

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::customer.documents')
        ->set('type', 'ktp')
        ->set('file', UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf'))
        ->call('storeDocument')
        ->assertHasNoErrors()
        ->assertSee('ktp.pdf');

    expect(Storage::disk('local')->files('documents/'.$user->id))->toHaveCount(1);
});
