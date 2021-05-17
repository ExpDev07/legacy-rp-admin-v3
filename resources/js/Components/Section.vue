<template>
    <div class="rounded-lg shadow-outline bg-secondary p-8 mb-10">

        <!-- Header -->
        <header class="mb-8">
            <slot name="header" />
        </header>

        <!-- Body -->
        <div class="mb-8">
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
    name: 'Section',
}
</script>

