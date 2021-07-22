<template>
    <div
        class="flex flex-col items-center justify-center h-screen p-5"
        :style="{ background: 'url(https://fivem.net/17c4e5d0b35262c228dcf66c98365982.jpg)' }"
    >
        <div class="max-w-3xl p-16 bg-white shadow">

            <!-- Flash message -->
            <div>
                <flash-message />
            </div>

            <!-- Content -->
            <div class="mb-8 text-center">
                <span class="mb-6 text-6xl text-gray-800 fab fa-steam"></span>
                <h1 class="mb-6 text-3xl font-semibold">
                    {{ t('login.title') }}
                </h1>
                <p class="text-red-500 font-bold mb-3 -mt-4" v-if="error">
                    {{ error }}
                </p>
                <p class="text-gray-700">
                    {{ t('login.description') }}
                </p>
            </div>

            <a class="block p-5 text-center text-white bg-indigo-600 hover:bg-orange-500" id="login-button" href="/auth/login/steam">
                {{ t('login.login') }}
            </a>

        </div>
    </div>
</template>

<script>
import FlashMessage from './../Components/FlashMessage';

export default {
    components: {
        FlashMessage,
    },
    props: {
        error: {
            type: String
        }
    },
    beforeCreate() {
        this.loadLocale(this.$page.lang);
    }
};
</script>
