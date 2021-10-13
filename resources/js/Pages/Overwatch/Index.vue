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

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button" @click="refresh">
                <span v-if="!isLoading">
                    <i class="mr-1 fa fa-refresh"></i>
                    {{ t('global.refresh') }}
                </span>

                <span v-else>
                    <i class="mr-1 fa fa-refresh animate-spin"></i>
                    {{ t('global.loading') }}
                </span>
            </button>
        </portal>

        <v-section>
            <div v-if="screenshot">
                <div class="flex">
                    <inertia-link
                        class="px-5 py-2 font-semibold text-white mr-3 rounded bg-blue-600 dark:bg-blue-500"
                        :href="'/players/' + screenshot.steam">
                        <i class="fas fa-user"></i>
                        {{ t("overwatch.profile", screenshot.id) }}
                    </inertia-link>

                    <badge class="border-blue-200 bg-blue-100 dark:bg-blue-700">
                        <span class="font-semibold">{{ t('overwatch.server', screenshot.server) }}</span>
                    </badge>
                </div>

                <img :src="screenshot.url" alt="Screenshot" class="mt-3 block" />
            </div>
            <p v-if="screenshotError" class="font-semibold text-danger dark:text-dark-danger">{{ screenshotError }}</p>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Badge from './../../Components/Badge';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge
    },
    data() {
        return {
            screenshot: null,
            screenshotError: null,
            isLoading: false
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
        }
    },
    mounted() {
        this.refresh();
    }
};
</script>
