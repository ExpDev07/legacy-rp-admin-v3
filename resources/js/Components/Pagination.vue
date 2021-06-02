<template>
    <div class="flex items-center justify-between mt-6 mb-1">

        <!-- Navigation -->
        <div class="flex flex-wrap">
            <inertia-link
                class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                :href="links.prev"
                v-if="links.prev"
                preserve-scroll
            >
                <i class="mr-1 fas fa-arrow-left"></i>
                {{ t("pagination.previous") }}
            </inertia-link>
            <inertia-link
                class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                :href="links.next"
                v-if="links.next"
                preserve-scroll
            >
                {{ t("pagination.next") }}
                <i class="ml-1 fas fa-arrow-right"></i>
            </inertia-link>
        </div>

        <!-- Meta -->
        <div class="font-semibold">
            {{ t("pagination.page", meta.current_page) }}
        </div>

    </div>
</template>

<script>
export default {
    props: {
        links: {
            type: Object,
            required: true,
        },
        meta: {
            type: Object,
            required: true,
        }
    },
}
</script>
