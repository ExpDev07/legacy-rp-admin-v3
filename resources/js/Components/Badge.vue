<template>
    <div class="badge px-5 py-1 border-2 rounded mobile:block mobile:w-full mobile:m-0 mobile:mb-3" :class="{ 'inline-block' : small }" @click="click && click($event);">
        <slot />
    </div>
</template>

<script>
export default {
    name: 'Badge',
    props: {
        small: Boolean,
        click: Function,
    }
}
</script>
