<template>
    <div class="p-8 mb-10 rounded-lg shadow bg-secondary dark:bg-dark-secondary mobile:p-4 mobile:py-2">

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
        }
    }
}
</script>
