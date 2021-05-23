<template>
    <div class="flex flex-col p-8 mb-5 bg-white rounded-lg shadow-sm">

        <!-- Header -->
        <header class="pb-6 mb-6 text-center border-b border-gray-200">
            <slot name="header" />
        </header>

        <!-- Body -->
        <div class="flex-grow mb-8 text-muted">
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

