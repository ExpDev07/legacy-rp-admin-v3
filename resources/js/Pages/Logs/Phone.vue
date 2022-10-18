<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('phone.title') }}
            </h1>
            <p>
                {{ t('phone.description') }}
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
                <form @submit.prevent autocomplete="off">
                    <input autocomplete="false" name="hidden" type="text" class="hidden"/>

                    <div class="flex flex-wrap mb-4">
                        <!-- Number -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="number">
                                {{ t('phone.number') }} <sup class="text-muted dark:text-dark-muted">*, C</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="number" placeholder="123-4567" v-model="filters.number">
                        </div>

                        <!-- Message -->
                        <div class="w-2/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="message">
                                {{ t('phone.message') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="message" placeholder="Some text message" v-model="filters.message">
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="w-full px-3 mt-3">
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">*
                            {{ t('global.search.exact') }}</small>
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">**
                            {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">C
                            {{ t('global.search.comma') }}</small>
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
                        <th class="px-6 py-4">{{ t('phone.from') }}</th>
                        <th class="px-6 py-4">{{ t('phone.to') }}</th>
                        <th class="px-6 py-4">{{ t('phone.message') }}</th>
                        <th class="px-6 py-4">
                            {{ t('logs.timestamp') }}
                        </th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="(log, index) in logs"
                        :key="log.id">
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ log.sender_number }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ log.receiver_number }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ log.message }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ log.timestamp*1000 | formatTime(true) }}
                        </td>
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
        VSection
    },
    props: {
        logs: {
            type: Array,
            required: true,
        },
        filters: {
            number: String,
            message: String,
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
                await this.$inertia.replace('/phoneLogs', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['logs', 'time', 'links', 'page'],
                });
            } catch (e) {
            }

            this.isLoading = false;
        },
    }
};
</script>
