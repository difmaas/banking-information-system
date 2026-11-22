@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1.5">
    <h1 class="text-2xl font-semibold tracking-tight text-foreground">{{ $title }}</h1>
    <p class="text-sm text-muted-foreground">{{ $description }}</p>
</div>
