<template>
    <div class="p-8 mb-10 rounded-lg shadow bg-secondary dark:bg-dark-secondary mobile:p-4 mobile:py-2">

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

