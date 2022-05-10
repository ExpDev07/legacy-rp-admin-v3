<template>
    <div class="flex">

        <!-- Nav -->
        <nav class="flex items-center justify-between w-full px-6 py-3 text-white bg-gray-900 shadow">
            <!-- Left side -->
            <p class="italic">
                <span class="px-4 py-1 ml-3 font-semibold text-black text-sm not-italic border-2 border-yellow-700 bg-warning rounded dark:bg-dark-warning text-sm float-right" v-if="$page.auth.player.isRoot">
                    <i class="fas fa-tools"></i>
                    {{ t('global.root') }}
                </span>

                <!-- Toggle Dark mode -->
                <button class="px-4 py-1 focus:outline-none font-semibold text-white text-sm rounded bg-gray-700 hover:bg-gray-600 text-base float-right" @click="toggleTheme" v-if="theme === 'light'">
                    <i class="fas fa-moon"></i>
                    {{ t("nav.dark") }}
                </button>
                <button class="px-4 py-1 focus:outline-none font-semibold text-black text-sm rounded bg-gray-400 hover:bg-gray-300 text-base float-right" @click="toggleTheme" v-else>
                    <i class="fas fa-sun"></i>
                    {{ t("nav.light") }}
                </button>
            </p>
        </nav>

    </div>
</template>

<script>
export default {
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
