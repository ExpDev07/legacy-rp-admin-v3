<template>
<div v-if="show">
    <!-- Success -->
    <div v-if="$page.flash.success" class="flex items-center justify-between rounded bg-green-500 text-white p-4 mb-8">
        <p>
            {{ $page.flash.success }}
        </p>
    </div>
    <!-- Error -->
    <div v-if="$page.flash.error" class="flex items-center justify-between rounded bg-red-500 text-white p-4 mb-8">
        <p>
            {{ $page.flash.error }}
        </p>
    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            show: true,
        };
    },
}
</script>
