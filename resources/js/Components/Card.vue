<template>
    <div class="flex flex-col rounded-lg shadow-sm bg-white p-8 mb-5">

        <!-- Header -->
        <header class="text-center border-b border-gray-200 pb-6 mb-6">
            <slot name="header" />
        </header>

        <!-- Body -->
        <div class="flex-grow text-muted mb-8">
            <slot />
        </div>

        <!-- Footer -->
        <footer>
            <slot name="footer" />
        </footer>

    </div>
</template>

<script>
export default {
    name: 'Card',
}
</script>

