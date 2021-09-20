<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('logs.logs') }}
            </h1>
            <p>
                {{ t('logs.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                    type="button" @click="refresh">
                <i class="mr-1 fa fa-refresh"></i>
                {{ t('logs.refresh') }}
            </button>
        </portal>

        <!-- Querying -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('logs.filter') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <!-- Identifier -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="identifier">
                                {{ t('logs.identifier') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="identifier" placeholder="steam:11000010df22c8b" v-model="filters.identifier">
                        </div>
                        <!-- Action -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="action">
                                {{ t('logs.action') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="action" :placeholder="t('logs.placeholder_action')" v-model="filters.action">
                        </div>
                        <!-- Server -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="server">
                                {{ t('logs.server_id') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="server" placeholder="3" type="number" v-model="filters.server">
                        </div>
                        <!-- Details -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-3 mt-3" for="details">
                                {{ t('logs.details') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="details" :placeholder="t('logs.placeholder_details')" v-model="filters.details">
                        </div>
                        <!-- After Date -->
                        <div class="w-1/6 px-3 pr-1 mobile:w-full mobile:mb-3">
                            <label class="block mb-3 mt-3" for="after-date">
                                {{ t('logs.after-date') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="after-date"
                                   type="date" placeholder="">
                        </div>
                        <!-- After Time -->
                        <div class="w-1/6 px-3 pl-1 mobile:w-full mobile:mb-3">
                            <label class="block mb-3 mt-3" for="after-time">
                                {{ t('logs.after-time') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="after-time" type="time" placeholder="">
                        </div>
                        <!-- Before Date -->
                        <div class="w-1/6 px-3 pr-1 mobile:w-full mobile:mb-3">
                            <label class="block mb-3 mt-3" for="before-date">
                                {{ t('logs.before-date') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="before-date" type="date" placeholder="">
                        </div>
                        <!-- Before Time -->
                        <div class="w-1/6 px-3 pl-1 mobile:w-full mobile:mb-3">
                            <label class="block mb-3 mt-3" for="before-time">
                                {{ t('logs.before-time') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="before-time" type="time" placeholder="">
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="w-full px-3 mt-3">
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">*
                            {{ t('global.search.exact') }}</small>
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">**
                            {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
                    </div>
                    <!-- Search button -->
                    <div class="w-full px-3 mt-3">
                        <button
                            class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg"
                            @click="refresh">
                            <span v-if="!isLoading">
                                <i class="fas fa-search"></i>
                                {{ t('logs.search') }}
                            </span>
                            <span v-else>
                                <i class="fas fa-cog animate-spin"></i>
                                {{ t('global.loading') }}
                            </span>
                        </button>
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Table -->
        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('logs.logs') }}
                </h2>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('logs.player') }}</th>
                        <th class="px-6 py-4">{{ t('logs.server_id') }}</th>
                        <th class="px-6 py-4">{{ t('logs.action') }}</th>
                        <th class="px-6 py-4">{{ t('logs.details') }}</th>
                        <th class="px-6 py-4">
                            {{ t('logs.timestamp') }}
                            <a href="#" :title="t('logs.toggle_diff')" @click="$event.preventDefault();showLogTimeDifference = !showLogTimeDifference">
                                <i class="fas fa-stopwatch"></i>
                            </a>
                        </th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="(log, index) in logs"
                        :key="log.id">
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link
                                class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400"
                                :href="'/players/' + log.steamIdentifier">
                                {{ playerName(log.steamIdentifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block" :title="t('global.server_timeout')">
                            <a class="font-semibold" :href="'/map#server_' + log.status.serverId"
                               :title="t('global.view_map')" v-if="log.status.status === 'online'">
                                {{ log.status.serverId }}
                            </a>
                            <span class="font-semibold" v-else>
                                {{ t('global.status.' + log.status.status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">{{ log.action }}</td>
                        <td class="px-6 py-3 border-t mobile:block" v-html="parseLog(log.details)">
                            {{ parseLog(log.details) }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block" v-if="showLogTimeDifference" :title="t('logs.diff_label')">
                            <span v-if="index+1 < logs.length">
                                {{ formatSecondDiff(stamp(log.timestamp) - stamp(logs[index+1].timestamp)) }}
                                <i class="fas fa-arrow-down"></i>
                            </span>
                            <span v-else>Start</span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block" v-else>{{ log.timestamp | formatTime(true) }}</td>
                    </tr>
                    <tr v-if="logs.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('logs.no_logs') }}
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
                            v-if="logs.length === 15"
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

        <modal :show.sync="showLogDetail">
            <template #header>
                <h1 class="dark:text-white">
                    {{ t('logs.detail.title') }}
                </h1>
                <p class="dark:text-dark-muted !-mt-3 italic">
                    {{ t('logs.detail.description', log_detail.user) }}
                </p>
            </template>

            <template #default>
                <pre class="text-lg block mb-2">{{ log_detail.reason }}</pre>
                {{ log_detail.description }}
            </template>

            <template #actions>
                <button type="button" class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400" @click="showLogDetail = false">
                    {{ t('global.close') }}
                </button>
            </template>
        </modal>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Pagination from './../../Components/Pagination';
import Modal from './../../Components/Modal';
import moment from "moment";

export default {
    layout: Layout,
    components: {
        Pagination,
        Modal,
        VSection,
    },
    props: {
        logs: {
            type: Array,
            required: true,
        },
        filters: {
            identifier: String,
            action: String,
            server: Number,
            details: String,
            before: Number,
            after: Number,
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
        },
        time: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            isLoading: false,
            showLogDetail: false,
            log_detail: {
                user: '',
                reason: '',
                description: ''
            },
            showLogTimeDifference: false
        };
    },
    methods: {
        formatSecondDiff(sec) {
            return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
        },
        stamp(time) {
            return this.$moment.utc(time).unix();
        },
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                const beforeDate = $('#before-date').val(),
                    beforeTime = $('#before-time').val(),
                    afterDate = $('#after-date').val(),
                    afterTime = $('#after-time').val();

                if (beforeDate && beforeTime) {
                    this.filters.before = Math.round((new Date(beforeDate + ' ' + beforeTime)).getTime() / 1000);

                    if (isNaN(this.filters.before)) {
                        this.filters.before = null;
                    }
                }

                if (afterDate && afterTime) {
                    this.filters.after = Math.round((new Date(afterDate + ' ' + afterTime)).getTime() / 1000);

                    if (isNaN(this.filters.after)) {
                        this.filters.after = null;
                    }
                }

                await this.$inertia.replace('/logs', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['logs', 'playerMap', 'time', 'links', 'page'],
                });
            } catch (e) {
            }

            this.isLoading = false;
        },
        parseDisconnectLog(details) {
            const regex = /(?<=\) has disconnected from the server .+? with reason: `)(.+?)(?=`\.)/gm;
            const matches = details.match(regex);
            const match = matches && matches.length === 1 && matches[0].trim() ? matches[0].trim() : null;

            if (match) {
                const descriptions = [
                    [/^Exiting/gmi, this.t('logs.detail.reasons.exited')],
                    [/^Disconnected|^You have disconnected from the server/gmi, this.t('logs.detail.reasons.disconnected')],
                    [/Game crashed: /gmi, this.t('logs.detail.reasons.crash')],
                    [/(?<=connection|You) timed out[!.]|^Timed out after/gmi, this.t('logs.detail.reasons.timeout')],
                    [/^You have been banned/gmi, this.t('logs.detail.reasons.banned')],
                    [/^The server is restarting/gmi, this.t('logs.detail.reasons.restart')],
                    [/^You have been kicked/gmi, this.t('logs.detail.reasons.kicked')],
                    [/^Your Job Priority expired/gmi, this.t('logs.detail.reasons.job')],
                    [/^Failed to sync doors/gmi, this.t('logs.detail.reasons.doors')],
                    [/^You have been globally banned from all OP-FW servers/gmi, this.t('logs.detail.reasons.global')],
                    [/^Entering Rockstar Editor/gmi, this.t('logs.detail.reasons.editor')],
                    [/^Reliable network event overflow/gmi, this.t('logs.detail.reasons.overflow')],
                    [/^Connecting to another server/gmi, this.t('logs.detail.reasons.another')],
                    [/^Obtaining configuration from server failed/gmi, this.t('logs.detail.reasons.config')],
                ];

                let description = '';
                for (let x in descriptions) {
                    const entry = descriptions[x];

                    if (entry[0].test(match)) {
                        description = entry[1];
                        break;
                    }
                }

                if (!description) {
                    description = this.t('logs.detail.reasons.unknown');
                }

                const html = $('<div />').append(
                    $('<a></a>', {
                        "data-reason" : match,
                        "data-description" : description,
                        "class": "text-yellow-800 dark:text-yellow-200 exit-log",
                        "href": "#"
                    }).text(match)
                ).html();

                return details.replace(match, html);
            }

            return details;
        },
        parseLog(details) {
            const regex = /(to|from) (inventory )?((trunk|glovebox|character|property|motel-\w+?|evidence|ground|locker-\w+?)-(\d+-)?\d+:\d+)/gmi;

            let inventories = [];

            let m;
            while ((m = regex.exec(details)) !== null) {
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }

                if (m.length > 3 && m[3] && !inventories.includes(m[3])) {
                    inventories.push(m[3]);
                }
            }

            for (let x = 0; x < inventories.length; x++) {
                details = details.replaceAll(inventories[x], '<a title="' + this.t('inventories.view') + '" class="text-indigo-600 dark:text-indigo-400" href="/inventory/' + inventories[x] + '">' + inventories[x] + '</a>');
            }

            return this.parseDisconnectLog(details);
        },
        playerName(steamIdentifier) {
            return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
        }
    },
    mounted() {
        const _this = this;
        $('body').on('click', 'a.exit-log', function(e) {
            e.preventDefault();
            const parent = $(this).closest('tr');

            _this.showLogDetail = true;
            _this.log_detail.user = $('td:first-child a', parent).text().trim();
            _this.log_detail.reason = $(this).data('reason');
            _this.log_detail.description = $(this).data('description');
        });

        if (this.filters.before) {
            const d = new Date(this.filters.before * 1000);

            $('#before-date').val(d.getFullYear() + '-' + ((d.getMonth() + 1) + '').padStart(2, '0') + '-' + (d.getDate() + '').padStart(2, '0'));
            $('#before-time').val(d.getHours() + ':' + d.getMinutes());
        }
        if (this.filters.after) {
            const d = new Date(this.filters.after * 1000);

            $('#after-date').val(d.getFullYear() + '-' + ((d.getMonth() + 1) + '').padStart(2, '0') + '-' + (d.getDate() + '').padStart(2, '0'));
            $('#after-time').val(d.getHours() + ':' + d.getMinutes());
        }
    }
};
</script>
