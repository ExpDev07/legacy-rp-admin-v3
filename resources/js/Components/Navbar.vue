<template>
    <div class="flex">

        <!-- Branding / Logo -->
        <div class="flex-shrink-0 w-56 px-12 py-4 text-center text-white bg-gray-900">
            <h1 class="text-lg">
                <inertia-link href="/">
                    Legacy<span class="font-bold">RP</span>
                </inertia-link>
            </h1>
        </div>

        <!-- Nav -->
        <nav class="flex items-center justify-between w-full px-12 py-4 text-white bg-gray-900 shadow">
            <!-- Left side -->
            <p class="italic">
                <!-- Toggle Dark mode -->
                <button class="px-4 py-1 focus:outline-none font-semibold text-white text-sm rounded bg-gray-700 hover:bg-gray-600 text-base float-right" @click="toggleTheme" v-if="theme === 'light'">
                    <i class="fas fa-moon"></i>
                    Dark Mode
                </button>
                <button class="px-4 py-1 focus:outline-none font-semibold text-black text-sm rounded bg-gray-400 hover:bg-gray-300 text-base float-right" @click="toggleTheme" v-else>
                    <i class="fas fa-sun"></i>
                    Light Mode
                </button>
            </p>

            <!-- Right side -->
            <div class="flex items-center space-x-6">
                <inertia-link class="hover:text-gray-100" v-bind:href="'/players/' + $page.auth.player.steamIdentifier">
                    {{ $page.auth.user.name }}
                </inertia-link>
                <inertia-link class="px-4 py-1 text-white bg-red-500 rounded hover:bg-red-600" method="POST" href="/logout">
                    Logout
                </inertia-link>
            </div>
        </nav>

    </div>
</template>

<script>
import Icon from './Icon';

export default {
    components: {
        Icon,
    },
    data() {
        return {
            theme: 'light',
        }
    },
    beforeMount() {
        this.updateTheme();
    },
    methods: {
        updateTheme() {
            const cachedTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : false;
            const userPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (cachedTheme)
                this.theme = cachedTheme;
            else if (userPrefersDark)
                this.theme = 'dark';

            $('html').removeClass('dark');
            if (this.theme === 'dark') {
                $('html').addClass('dark');
            }
        },
        toggleTheme() {
            if ($('html').hasClass('dark')) {
                localStorage.setItem('theme', 'light');
            } else {
                localStorage.setItem('theme', 'dark');
            }

            this.updateTheme();
        }
    },
}
</script>
