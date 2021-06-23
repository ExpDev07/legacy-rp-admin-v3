<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("home.title") }}
            </h1>
            <p>
                {{ t("home.welcome", $page.auth.user.name) }}
            </p>
            <div class="p-4 pl-6 italic border-l-4 border-gray-300 bg-gray-100 shadow-lg dark:border-gray-500 dark:bg-gray-700 dark:text-gray-100">
                <span class="mb-1 block">
                    {{ quote.quote }}
                </span>
                <span class="text-xs">
                    - {{ quote.author }}
                </span>
            </div>
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
    props: {
        quote: {
            type: String,
            required: true,
        },
    }
}
</script>
