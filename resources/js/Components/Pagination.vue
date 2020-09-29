<template>
    <div class="flex items-center justify-between mt-6 mb-1">
        <!-- Navigation -->
        <div class="flex flex-wrap">
            <inertia-link class="bg-indigo-600 hover:bg-orange-500 text-white rounded px-4 py-2 mr-3" v-if="links.prev" v-bind:href="links.prev" preserve-scroll>
                <i class="fas fa-arrow-left mr-1"></i>
                Previous
            </inertia-link>
            <inertia-link class="bg-indigo-600 hover:bg-orange-500 text-white rounded px-4 py-2 mr-1" v-if="links.next" v-bind:href="links.next" preserve-scroll>
                Next
                <i class="fas fa-arrow-right ml-1"></i>
            </inertia-link>
        </div>
        <!-- Meta -->
        <div class="font-semibold">
            Page {{ meta.current_page }}
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
