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

        <v-section class="overflow-x-auto">
            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('players.form.identifier') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.playtime') }}</th>
                        <th class="px-6 py-4">{{ t('players.ban_reason') }}</th>
                        <th class="w-64 px-6 py-4"></th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="player in players" v-bind:key="player.user_id">
                        <td class="px-6 py-3 border-t mobile:block">{{ player.steam_identifier }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.player_name }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.playtime | humanizeSeconds }}</td>
                        <td class="px-6 py-3 border-t mobile:block text-sm font-mono" :title="player.reason ? player.reason : t('players.ban.no_reason')">
                            {{ player.reason ? cutText(player.reason) : t('players.ban.no_reason') }}
                        </td>
                        <td class="px-6 py-3 text-center border-t mobile:block">
                            <span
                                class="block px-4 py-2 text-white rounded"
                                :class="player.expire ? 'bg-red-600 dark:bg-red-700' : 'bg-red-500 dark:bg-red-600'"
                                :title="localizeBan(player)"
                            >
                                {{ t('global.banned') }}
                                <span class="block text-xxs">
                                    {{ t('global.by', formatBanCreator(player.creator_name, 'creator_name')) }}
                                </span>
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/players/' + player.steam_identifier">
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
    },
    data() {
        return {
            isLoading: false
        };
    },
    methods: {
        cutText(text) {
            if (text.length > 120) {
                return text.substring(0, 120) + '...';
            }

            return text;
        },
        localizeBan(ban) {
            return ban.expire
                ? this.t('players.show.ban', this.formatBanCreator(ban.creator_name), this.$options.filters.formatTime(ban.expire))
                : this.t('players.ban.forever', this.formatBanCreator(ban.creator_name));
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
