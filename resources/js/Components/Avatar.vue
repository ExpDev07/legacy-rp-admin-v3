<template>
    <img
        class="inline-block w-8 h-8 rounded-full ring-2 ring-white"
        :src="src"
        :alt="alt"
    />
</template>

<script>
export default {
    name: 'Avatar',
    props: {
        src: {
            type: String,
            required: true,
        },
        alt: {
            type: String,
            default: 'Avatar',
        }
    },
}
</script>
