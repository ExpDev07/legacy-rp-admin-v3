<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('advanced.title') }}
            </h1>
            <p>
                {{ t('advanced.description') }}
            </p>
        </portal>

        <!-- Querying -->
        <v-section>
            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <p class="w-full mb-4 font-bold text-red-700 dark:text-red-500" v-if="error">
                            {{ error }}
                        </p>

                        <!-- Table -->
                        <div class="w-1/4 px-3">
                            <label class="block mb-2" for="table">
                                {{ t('advanced.form.table') }}
                            </label>
                            <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="table" v-model="filters.table" @change="tableUpdate">
                                <option v-for="(_, tablename) in config" :value="tablename">
                                    {{ tablename }}
                                </option>
                            </select>
                        </div>

                        <!-- Field -->
                        <div class="w-1/4 px-3">
                            <label class="block mb-2" for="field">
                                {{ t('advanced.form.field') }}
                            </label>
                            <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" v-for="(fields, tablename) in config" v-if="tablename === tableName" id="field" v-model="filters.field">
                                <option v-for="field in fields" :value="field">
                                    {{ field }}
                                </option>
                            </select>
                        </div>

                        <!-- Type -->
                        <div class="w-1/4 px-3">
                            <label class="block mb-2" for="type">
                                {{ t('advanced.form.type') }}
                            </label>
                            <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="type" v-model="filters.searchType">
                                <option value="exact">{{ t('advanced.form.types.exact') }}</option>
                                <option value="more">{{ t('advanced.form.types.more') }}</option>
                                <option value="less">{{ t('advanced.form.types.less') }}</option>
                                <option value="like">{{ t('advanced.form.types.like') }}</option>
                                <option value="not_null">{{ t('advanced.form.types.not_null') }}</option>
                                <option value="null">{{ t('advanced.form.types.null') }}</option>
                                <option value="not_empty">{{ t('advanced.form.types.not_empty') }}</option>
                                <option value="empty">{{ t('advanced.form.types.empty') }}</option>
                            </select>
                        </div>

                        <!-- Value -->
                        <div class="w-1/4 px-3">
                            <label class="block mb-2" for="value">
                                {{ t('advanced.form.value') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="value" v-model="filters.value">
                        </div>
                    </div>
                    <!-- Search button -->
                    <div class="w-full px-3 mt-3">
                        <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                            <span v-if="!isLoading">
                                <i class="fas fa-search"></i>
                                {{ t('advanced.search') }}
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
                    {{ t('advanced.results') }}
                </h2>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4" v-for="title in header">
                            <span v-if="title">{{ t('advanced.' + searchedTable + '.' + title) }}</span>
                        </th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="result in results">
                        <td class="px-6 py-3 border-t" v-for="entry in result">
                            <inertia-link class="block py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-if="typeof entry === 'object' && 'link' in entry" :href="entry.link.target">
                                {{ entry.link.label }}
                            </inertia-link>
                            <div v-else-if="typeof entry === 'object' && 'pre' in entry" class="collapsible">
                                <div class="cursor-pointer text-blue-600 dark:text-blue-400 col-show">{{ t('advanced.show') }}</div>
                                <div class="col-data overflow-hidden h-0">
                                    <a href="#" class="block mb-2 text-blue-600 dark:text-blue-400">{{ t('advanced.hide') }}</a>
                                    <pre class="whitespace-pre-wrap text-xs" v-html="entry.pre">{{ entry.pre }}</pre>
                                </div>
                            </div>
                            <span v-else-if="typeof entry === 'object' && 'time' in entry">
                                {{ entry.time | formatTime(true) }}
                            </span>
                            <span v-else>
                                {{ entry }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="results.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('advanced.no_results') }}
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
                            v-if="results.length === 15"
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

export default {
    layout: Layout,
    components: {
        VSection,
    },
    props: {
        results: {
            type: Array,
            required: true,
        },
        header: {
            type: Array,
            required: true,
        },
        filters: {
            table: String,
            field: String,
            searchType: String,
            value: String,
        },
        links: {
            type: Object,
            required: true,
        },
        config: {
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
        },
        error: {
            type: String,
            required: true,
        },
        searchedTable: {
            type: String,
            required: true,
        }
    },
    data() {
        return {
            isLoading: false,
            tableName: this.filters.table,
        };
    },
    methods: {
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/advanced', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['results', 'header', 'time', 'links', 'page', 'error', 'searchedTable'],
                });

                this.initHandlers();
            } catch (e) {
            }

            this.isLoading = false;
        },
        tableUpdate(e) {
            const value = $('#table').val();

            this.tableName = value;

            this.filters.field = this.config[value][0];
        },
        initHandlers() {
            $('.collapsible:not(.handled) > .col-show').on('click', function() {
                const parent = $(this).closest('.collapsible');

                $('.col-show', parent).addClass('hidden');
                $('.col-data', parent).removeClass('h-0');
            });
            $('.collapsible:not(.handled) a').on('click', function(e) {
                e.preventDefault();

                const parent = $(this).closest('.collapsible');

                $('.col-show', parent).removeClass('hidden');
                $('.col-data', parent).addClass('h-0');
            });

            $('.collapsible').addClass('handled');
        }
    },
    mounted() {
        this.initHandlers();
    }
};
</script>
