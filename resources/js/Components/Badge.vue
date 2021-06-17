<template>
    <div class="px-5 py-1 border-2 rounded" :class="{ 'inline-block' : small }">
        <slot />
    </div>
</template>

<script>
export default {
    name: 'Badge',
    props: {
        small: Boolean,
    }
}
</script>
