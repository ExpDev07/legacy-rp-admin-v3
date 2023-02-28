<template>
    <div class="flex flex-col p-8 mb-5 bg-white dark:bg-gray-600 rounded-lg shadow-sm" :class="{ 'hidden card-deleted bg-red-100 opacity-60' : deleted }">

        <!-- Header -->
        <header class="text-center" :class="{ 'border-red-300' : deleted, 'pb-6 mb-6 border-b border-gray-200 dark:border-gray-400' : !no_body }">
            <slot name="header" />
        </header>

        <!-- Body -->
        <div class="flex-grow text-muted dark:text-dark-muted" v-if="!no_body" :class="{'mb-8' : !no_footer}">
            <slot />
        </div>

        <!-- Footer -->
        <footer v-if="!no_footer">
            <slot name="footer" />
        </footer>

    </div>
</template>

<script>
export default {
    name: 'Card',
    props: {
        deleted: Boolean,
        no_body: Boolean,
        no_footer: Boolean,
    },
}
</script>
