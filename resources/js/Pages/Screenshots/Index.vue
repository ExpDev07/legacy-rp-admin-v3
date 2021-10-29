<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('screenshot.screenshots') }}
            </h1>
            <p>
                {{ t('screenshot.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button" @click="refresh">
                <i class="mr-1 fa fa-refresh"></i>
                {{ t('global.refresh') }}
            </button>
        </portal>

        <!-- Table -->
        <v-section class="overflow-x-auto">
            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('screenshot.player') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.screenshot') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.note') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.created_at') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="screenshot in screenshots"
                        :key="screenshot.filename">
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + screenshot.steam_identifier">
                                {{ playerName(screenshot.steam_identifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <a :href="'/export/screenshot/' + screenshot.filename" target="_blank" class="text-indigo-600 dark:text-indigo-400">{{ t('screenshot.view') }}</a>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <i class="fas fa-cogs mr-1" v-if="screenshot.system"></i>
                            {{ screenshot.note || 'N/A' }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block" v-if="screenshot.created_at">{{ screenshot.created_at * 1000 | formatTime(true) }}</td>
                        <td class="px-6 py-3 border-t mobile:block" v-else>{{ t('global.unknown') }}</td>
                    </tr>
                    <tr v-if="screenshots.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('screenshot.no_screenshots') }}
                        </td>
                    </tr>
                </table>
            </template>

            <template #footer>
                <div class="flex items-center justify-between mt-6 mb-1">

                    <!-- Navigation -->
                    <div class="flex flex-wrap">
                        <inertia-link
                            class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                            :href="links.prev"
                            v-if="page >= 2"
                        >
                            <i class="mr-1 fas fa-arrow-left"></i>
                            {{ t("pagination.previous") }}
                        </inertia-link>
                        <inertia-link
                            class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                            v-if="screenshots.length === 20"
                            :href="links.next"
                        >
                            {{ t("pagination.next") }}
                            <i class="ml-1 fas fa-arrow-right"></i>
                        </inertia-link>
                    </div>

                    <!-- Meta -->
                    <div class="font-semibold">
                        {{ t("pagination.page", page) }}
                    </div>

                </div>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Pagination from './../../Components/Pagination';

export default {
    layout: Layout,
    components: {
        Pagination,
        VSection,
    },
    props: {
        screenshots: {
            type: Array,
            required: true,
        },
        playerMap: {
            type: Object,
            required: true,
        },
        links: {
            type: Object,
            required: true,
        },
        page: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            isLoading: false
        };
    },
    methods: {
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/screenshots', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'screenshots', 'playerMap', 'links', 'page' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
        playerName(steamIdentifier) {
            return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
        }
    }
};
</script>
