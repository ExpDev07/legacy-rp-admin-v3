<template>
    <img
        class="inline-block rounded-full ring-2 ring-white h-8 w-8"
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
