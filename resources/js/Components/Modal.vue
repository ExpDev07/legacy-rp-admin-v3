<template>
    <portal to="modals" v-if="show">

        <!-- Backdrop -->
        <div
            class="absolute inset-0 overflow-hidden flex items-start justify-center"
            style="z-index: 9999; background-color: rgba(0, 0, 0, .85);"
            tabindex="-1"
            role="dialog"
            @mousedown.self="hide"
        >

            <!-- Container -->
            <div class="my-20 max-w-3xl w-full shadow rounded-md bg-white" role="document" v-bind="$attrs">

                <!-- Content part -->
                <div class="py-8 px-10 space-y-10">

                    <!-- Header -->
                    <header class="prose max-w-full text-center">
                        <slot name="header" />
                    </header>

                    <!-- Main -->
                    <main>
                        <slot />
                    </main>

                </div>

                <!-- Actions -->
                <footer class="py-4 px-10 space-x-3 flex items-center justify-end">
                    <slot name="actions" />
                </footer>

            </div>

        </div>

    </portal>
</template>

<script>
export default {
    name: 'Modal',
    inheritAttrs: false,
    props: {
        show: Boolean,
    },
    methods: {
        /**
         * Hides the modal.
         */
        hide() {
            this.$emit('update:show', false);
        }
    },
}
</script>
