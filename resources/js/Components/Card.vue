<template>
    <div class="flex flex-col p-8 mb-5 bg-white rounded-lg shadow-sm" :class="{ 'card-deleted bg-red-100 opacity-60' : deleted }">

        <!-- Header -->
        <header class="pb-6 mb-6 text-center border-b border-gray-200" :class="{ 'border-red-300' : deleted }">
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
    props: ['deleted'],
    name: 'Card',
}
</script>

