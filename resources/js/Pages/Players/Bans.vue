<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('players.ban.title') }}
            </h1>
            <p>
                {{ t('players.ban.description') }}
            </p>
        </portal>

        <!-- Search -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.search') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <div class="w-1/2 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4 font-semibold" for="banHash">
                                {{ t('players.ban.hash') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="banHash" name="banHash" placeholder="b60f832e-0c78-42ed-acae-9c89b5f14265" v-model="filters.banHash">
                        </div>

                        <div class="w-1/2 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4 font-semibold" for="reason">
                                {{ t('players.ban.reason') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="reason" name="reason" placeholder="L Bozo" v-model="filters.reason">
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="w-full px-3 mt-3">
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">** {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
                    </div>
                </form>
                <!-- Search button -->
                <div class="w-full mt-3">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                        <span v-if="!isLoading">
                            <i class="fas fa-search"></i>
                            {{ t('players.ban.search_btn') }}
                        </span>
                        <span v-else>
                            <i class="fas fa-cog animate-spin"></i>
                            {{ t('global.loading') }}
                        </span>
                    </button>
                </div>
            </template>
        </v-section>

        <v-section class="overflow-x-auto">
            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('players.form.identifier') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('players.ban_time') }}</th>
                        <th class="px-6 py-4">{{ t('players.ban_reason') }}</th>
                        <th class="px-6 py-4">{{ t('players.ban_creator') }}</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="player in players" v-bind:key="player.user_id">
                        <td class="px-6 py-3 border-t mobile:block">{{ player.license_identifier }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.player_name }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ localizeBan(player) }}</td>
                        <td class="px-6 py-3 border-t mobile:block text-xs font-mono" :title="player.reason ? player.reason : t('players.ban.no_reason')">
                            {{ player.reason ? cutText(player.reason) : t('players.ban.no_reason') }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ formatBanCreator(player.creator_name, 'creator_name') }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/players/' + player.license_identifier">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="players.length === 0">
                        <td class="px-6 py-6 text-center border-t mobile:block" colspan="100%">
                            {{ t('players.none') }}
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
                            v-if="players.length === 15"
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
import Badge from './../../Components/Badge';
import Pagination from './../../Components/Pagination';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge,
        Pagination,
    },
    props: {
        players: {
            type: Array,
            required: true,
        },
        links: {
            type: Object,
            required: true,
        },
        page: {
            type: Number,
            required: true,
        },
        filters: {
            banHash: String,
            reason: String,
        },
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
                await this.$inertia.replace(window.location.pathname, {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'players', 'links', 'page' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
        cutText(text) {
            if (text.length > 120) {
                return text.substring(0, 90) + '...';
            }

            return text;
        },
        localizeBan(ban) {
            return ban.expire
                ? this.t('players.show.ban_text', this.$options.filters.formatTime((ban.timestamp + ban.expire) * 1000))
                : this.t('players.show.ban_forever_text');
        },
        formatBanCreator(creator) {
            if (!creator) {
                return this.t('global.system');
            }
            return creator;
        }
    }
}
</script>
