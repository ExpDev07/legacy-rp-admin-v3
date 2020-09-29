<template>
    <div>
        <h1 class="text-3xl font-bold mb-8">
            Dashboard
        </h1>
        <p class="mb-12 leading-normal">
            Hello, {{ $page.auth.user.name }}. Welcome to the dashboard!
        </p>
    </div>
</template>

<script>
import Layout from './../Layouts/App';

export default {
    layout: Layout,
};
</script>
