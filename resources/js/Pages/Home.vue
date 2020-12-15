<template>
    <div>
        <div class="prose">
            <h1>
                Dashboard
            </h1>
            <p>
                Hello, {{ $page.auth.user.name }}. Welcome to the dashboard!
            </p>
        </div>
    </div>
</template>

<script>
import Layout from './../Layouts/App';

export default {
    layout: Layout,
};
</script>
