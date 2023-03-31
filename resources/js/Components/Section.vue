<template>
    <div class="p-8 mb-10 rounded-lg shado mobile:p-4 mobile:py-2" :class="bright ? 'bg-gray-300 dark:bg-gray-600 scale-105' : 'bg-secondary dark:bg-dark-secondary'">

        <!-- Header -->
        <header class="mb-8" v-if="!noHeader">
            <slot name="header" />
        </header>

        <!-- Body -->
        <div :class="!noFooter ? 'mb-8' : ''">
            <slot />
        </div>

        <!-- Footer -->
        <footer v-if="!noFooter">
            <slot name="footer" />
        </footer>

    </div>
</template>

<script>
export default {
    name: 'Section',
    props: {
        noFooter: {
            type: Boolean,
            default: false
        },
        noHeader: {
            type: Boolean,
            default: false
        },
        bright: {
            type: Boolean,
            default: false
        }
    }
}
</script>
