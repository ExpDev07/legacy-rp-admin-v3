<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("home.title") }}
            </h1>
            <p>
                {{ t("home.welcome", $page.auth.user.name) }}
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
