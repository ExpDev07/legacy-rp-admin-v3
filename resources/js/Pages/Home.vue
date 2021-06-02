<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                Dashboard
            </h1>
            <p>
                Hello, {{ $page.auth.user.name }}. Welcome to the dashboard!
            </p>
        </portal>

        <lottie
            :animationSpeed="1"
            :options="{ autoplay: true, animationData: 'https://assets2.lottiefiles.com/private_files/lf30_y9czxcb9.json' }"
        />

    </div>
</template>

<script>
import Lottie from 'vue-lottie';
import Layout from './../Layouts/App';

export default {
    layout: Layout,
    components: {
        Lottie,
    },
}
</script>
