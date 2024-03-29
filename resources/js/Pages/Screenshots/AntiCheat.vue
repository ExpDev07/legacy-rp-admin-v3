<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('screenshot.anti_cheat') }}
            </h1>
            <p>
                {{ t('screenshot.anti_cheat_description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button" @click="refresh">
                <i class="mr-1 fa fa-refresh"></i>
                {{ t('global.refresh') }}
            </button>
        </portal>

        <!-- Table -->
        <v-section class="overflow-x-auto" :noHeader="true">
            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('screenshot.player') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.screenshot') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.note') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.created_at') }}</th>
                        <th class="w-64 px-6 py-4">{{ t('players.form.banned') }}?</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="screenshot in formattedScreenshots"
                        :key="screenshot.url">
                        <template v-if="screenshot.isBan">
                            <td class="px-6 py-2 border-t text-center mobile:block">
                                <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-red-600 rounded dark:bg-red-400" :href="'/players/' + screenshot.license_identifier">
                                    {{ screenshot.player_name }}
                                </inertia-link>
                            </td>
                            <td class="px-6 py-2 border-t mobile:block italic text-gray-600 dark:text-gray-400" colspan="2">
                                Banned indefinitely for <span class="font-semibold">{{ screenshot.reason }}</span>
                            </td>
                            <td class="px-6 py-2 border-t mobile:block italic text-gray-600 dark:text-gray-400">
                                {{ screenshot.timestamp * 1000 | formatTime(true) }}
                            </td>
                            <td class="px-6 py-2 border-t mobile:block">&nbsp;</td>
                        </template>
                        <template v-else>
                            <td class="px-6 py-3 border-t mobile:block">
                                <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + screenshot.license_identifier">
                                    {{ screenshot.player_name }}
                                </inertia-link>
                            </td>
                            <td class="px-6 py-3 border-t mobile:block">
                                <a :href="screenshot.url" target="_blank" class="text-indigo-600 dark:text-indigo-400">{{ t('screenshot.view', screenshot.url.split(".").pop()) }}</a>
                            </td>
                            <td class="px-6 py-3 border-t mobile:block">
                                {{ screenshot.details || 'N/A' }}
                            </td>
                            <td class="px-6 py-3 border-t mobile:block" v-if="screenshot.timestamp">{{ screenshot.timestamp * 1000 | formatTime(true) }}</td>
                            <td class="px-6 py-3 border-t mobile:block" v-else>{{ t('global.unknown') }}</td>
                            <td class="px-6 py-3 text-center border-t mobile:block">
                                <span
                                    class="block px-4 py-2 text-white rounded"
                                    :class="screenshot.ban.reason ? 'bg-red-600 dark:bg-red-700' : 'bg-red-500 dark:bg-red-600'"
                                    :title="screenshot.ban.reason ? screenshot.ban.reason : t('players.ban.no_reason')"
                                    v-if="screenshot.ban"
                                >
                                    {{ t('global.banned') }}
                                    <span class="block text-xxs">
                                        {{ t('global.by', screenshot.ban.creator_name ? screenshot.ban.creator_name : 'System') }}
                                    </span>
                                </span>
                                <span class="block px-4 py-2 text-white bg-green-500 rounded dark:bg-green-600" v-else>
                                    {{ t('global.not_banned') }}
                                </span>
                            </td>
                        </template>
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
        banMap: {
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
        const screenshots = this.screenshots.map(screenshot => {
            screenshot.ban = this.getBanInfo(screenshot.license_identifier);
            screenshot.isBan = !screenshot.url.startsWith("http");

            return screenshot;
        });

        return {
            isLoading: false,

            formattedScreenshots: screenshots
        };
    },
    methods: {
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/anti_cheat', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'screenshots', 'playerMap', 'links', 'page' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
        getBanInfo(licenseIdentifier, key) {
            const ban = licenseIdentifier in this.banMap ? this.banMap[licenseIdentifier] : null;

            if (key) {
                return ban && key in ban ? ban[key] : null;
            }

            return ban;
        }
    }
};
</script>
