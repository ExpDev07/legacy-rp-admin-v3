<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('overwatch.title') }}
            </h1>
            <p>
                {{ t('overwatch.description') }}
            </p>
        </portal>

        <ScreenshotAttacher :close="screenshotAttached" :steam="screenshot.steam" :url="screenshot.url" v-if="isAttaching" />

        <v-section class="-mt-10 max-w-screen-lg">
            <div class="-mt-8">
                <div class="flex justify-between">
                    <div class="flex">
                        <inertia-link
                            class="px-5 py-2 font-semibold text-white mr-3 rounded bg-blue-600 dark:bg-blue-500"
                            :href="'/players/' + screenshot.steam"
                            v-if="screenshot">
                            <i class="fas fa-user"></i>
                            {{ t("overwatch.profile", screenshot.id) }}
                        </inertia-link>

                        <badge class="border-blue-200 bg-blue-100 dark:bg-blue-700" v-if="screenshot">
                            <span class="font-semibold">{{ t('overwatch.server', screenshot.server) }}</span>
                        </badge>
                    </div>

                    <div class="flex">
                        <button class="px-5 py-2 font-semibold text-white bg-primary rounded dark:bg-dark-primary mr-3" @click="isAttaching = true" v-if="screenshot">
                            <i class="fas fa-paper-plane mr-1"></i>
                            {{ t('screenshot.title') }}
                        </button>

                        <button class="px-5 py-2 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400" @click="refresh">
                            <span v-if="!isLoading">
                                <i class="mr-1 fa fa-refresh"></i>
                                {{ t('global.refresh') }}
                            </span>

                            <span v-else>
                                <i class="mr-1 fa fa-refresh animate-spin"></i>
                                {{ t('global.loading') }}
                            </span>
                        </button>
                    </div>
                </div>

                <a :href="screenshot.url" class="mt-5 block" target="_blank" v-if="screenshot">
                    <img :src="screenshot.url" alt="Screenshot" class="block" />
                </a>
            </div>

            <p v-if="screenshotError" class="font-semibold text-danger dark:text-dark-danger m-0">{{ screenshotError }}</p>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Badge from './../../Components/Badge';
import ScreenshotAttacher from './../../Components/ScreenshotAttacher';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge,
        ScreenshotAttacher
    },
    data() {
        return {
            screenshot: null,
            screenshotError: null,
            isLoading: false,
            isAttaching: false
        };
    },
    methods: {
        async refresh() {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;

            try {
                const data = await axios.get('/api/randomScreenshot');

                if (data.data && data.data.status) {
                    this.screenshot = data.data.data;
                    this.screenshotError = null;
                } else {
                    this.screenshot = null;
                    this.screenshotError = data.data.message;
                }
            } catch(e) {}

            this.isLoading = false;
        },
        screenshotAttached(status, message) {
            this.isAttaching = false;

            if (message) {
                alert(message);
            }
        }
    },
    mounted() {
        this.refresh();
    }
};
</script>
