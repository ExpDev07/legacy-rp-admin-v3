<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('characters.title') }}
            </h1>
            <p>
                {{ t('characters.description') }}
            </p>
        </portal>

        <!-- Querying -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('characters.filter') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <!-- Character ID -->
                        <div class="w-1/3 px-3" v-if="!advanced">
                            <label class="block mb-2" for="character_id">
                                {{ t('characters.form.character_id') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="number" id="character_id" placeholder="16802" v-model="filters.character_id">
                        </div>
                        <!-- Name -->
                        <div class="w-1/3 px-3" v-if="!advanced">
                            <label class="block mb-2" for="name">
                                {{ t('characters.form.name') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="name" placeholder="Charlie Ives" v-model="filters.name">
                        </div>
                        <!-- Vehicle Plate -->
                        <div class="w-1/3 px-3" v-if="!advanced">
                            <label class="block mb-2" for="vehicle_plate">
                                {{ t('characters.form.plate') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="vehicle_plate" placeholder="95MBH817" v-model="filters.vehicle_plate">
                        </div>
                        <!-- Phone Number -->
                        <div class="w-1/4 px-3" v-if="!advanced">
                            <label class="block mb-2" for="phone">
                                {{ t('characters.form.phone') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="phone" placeholder="606-0992" v-model="filters.phone">
                        </div>
                        <!-- Job -->
                        <div class="w-3/4 px-3" v-if="!advanced">
                            <label class="block mb-2" for="job">
                                {{ t('characters.form.job') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="job" placeholder="Government Waste Collector Employee" v-model="filters.job">
                        </div>
                        <!-- Description -->
                        <div class="w-full px-3 mt-3" v-if="!advanced">
                            <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">* {{ t('global.search.exact') }}</small>
                            <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">** {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
                        </div>
                        <div class="w-full flex flex-wrap mb-4" v-if="advanced">
                            <div class="w-full mb-3 text-red-700 dark:text-red-500" v-if="error">
                                {{ error }}
                            </div>
                            <!-- Where Field -->
                            <div class="w-1/4 px-3">
                                <label class="block mb-2" for="field">
                                    {{ t('characters.form.field') }}
                                </label>
                                <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="field" v-model="filters.field">
                                    <!-- Character -->
                                    <optgroup :label="t('characters.form.fields.character')">
                                        <option value="characters.character_id">character_id</option>
                                        <option value="characters.backstory">backstory</option>
                                        <option value="characters.bank">bank</option>
                                        <option value="characters.cash">cash</option>
                                        <option value="characters.date_of_birth">date_of_birth</option>
                                        <option value="characters.department_name">department_name</option>
                                        <option value="characters.first_name">first_name</option>
                                        <option value="characters.gender">gender</option>
                                        <option value="characters.job_name">job_name</option>
                                        <option value="characters.last_name">last_name</option>
                                        <option value="characters.phone_number">phone_number</option>
                                        <option value="characters.position_name">position_name</option>
                                        <option value="characters.steam_identifier">steam_identifier</option>
                                        <option value="characters.stocks_balance">stocks_balance</option>
                                    </optgroup>

                                    <!-- Vehicle -->
                                    <optgroup :label="t('characters.form.fields.vehicle')">
                                        <option value="vehicles.vehicle_id">vehicle_id</option>
                                        <option value="vehicles.model_name">model_name</option>
                                        <option value="vehicles.owner_cid">owner_cid</option>
                                        <option value="vehicles.plate">plate</option>
                                    </optgroup>
                                </select>
                            </div>
                            <!-- Match Type -->
                            <div class="w-1/4 px-3">
                                <label class="block mb-2" for="type">
                                    {{ t('characters.form.type') }}
                                </label>
                                <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="type" v-model="filters.type">
                                    <option value="exact">{{ t('characters.form.types.exact') }}</option>
                                    <option value="more">{{ t('characters.form.types.more') }}</option>
                                    <option value="less">{{ t('characters.form.types.less') }}</option>
                                    <option value="like">{{ t('characters.form.types.like') }}</option>
                                </select>
                            </div>
                            <!-- Where Value -->
                            <div class="w-2/4 px-3">
                                <label class="block mb-2" for="value">
                                    {{ t('characters.form.value') }}
                                </label>
                                <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="value" v-model="filters.value">
                            </div>
                        </div>
                        <!-- Search button -->
                        <div class="w-full px-3 mt-3">
                            <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                            <span v-if="!isLoading">
                                <i class="fas fa-search"></i>
                                {{ t('characters.search') }}
                            </span>
                                <span v-else>
                                <i class="fas fa-cog animate-spin"></i>
                                {{ t('global.loading') }}
                            </span>
                            </button>
                        </div>
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Table -->
        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('characters.title') }}
                </h2>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap" :class="{ 'table-fixed' : advanced }">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('characters.result.player') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.character_id') }}</th>
                        <th class="px-6 py-4" v-if="!advanced">{{ t('characters.form.phone') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.name') }}</th>
                        <th class="px-6 py-4" v-if="!advanced">{{ t('characters.result.gender') }}</th>
                        <th class="px-6 py-4" v-if="!advanced">{{ t('characters.result.job') }}</th>
                        <th class="px-6 py-4 w-character_advanced" v-if="advanced">{{ t('characters.result.advanced.title') }}</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="character in characters.data" :key="character.id">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + character.steamIdentifier">
                                {{ playerName(character.steamIdentifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ character.id }}</td>
                        <td class="px-6 py-3 border-t" v-if="!advanced">{{ character.phoneNumber }}</td>
                        <td class="px-6 py-3 border-t">
                            {{ character.firstName }} {{ character.lastName }}
                        </td>
                        <td class="px-6 py-3 border-t" v-if="!advanced">
                            {{ character.gender | formatGender(t) }}
                        </td>
                        <td class="px-6 py-3 border-t" v-if="!advanced">
                            {{ character.jobName || t('global.none') }} /
                            {{ character.departmentName || t('global.none') }} /
                            {{ character.positionName || t('global.none') }}
                        </td>
                        <td class="px-6 py-3 border-t w-character_advanced font-mono text-sm" v-if="advanced" v-html="advancedData(character)">
                            {{ advancedData(character) }}
                        </td>
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/players/' + character.steamIdentifier + '/characters/' + character.id + '/edit'">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="characters.data.length === 0">
                        <td class="px-6 py-6 text-center border-t" colspan="100%">
                            {{ t('characters.none') }}
                        </td>
                    </tr>
                </table>
            </template>

            <template #footer>
                <pagination v-bind:links="characters.links" v-bind:meta="characters.meta" />
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
        characters: {
            type: Object,
            required: true,
        },
        filters: {
            character_id: Number,
            name: String,
            vehicle_plate: String,
            phone: String,
            job: String,
            field: String,
            value: String,
        },
        playerMap: {
            type: Object,
            required: true,
        },
        vehicleMap: {
            type: Object,
            required: true,
        },
        time: {
            type: Number,
            required: true,
        },
        advanced: {
            type: Boolean,
            required: true,
        },
        error: {
            type: String,
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

            let filters = this.filters;
            if (this.advanced) {
                filters.advanced = '';
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/characters', {
                    data: filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'characters', 'playerMap', 'time', 'error' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
        playerName(steamIdentifier) {
            return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
        },
        vehicleList(characterId) {
            return characterId in this.vehicleMap ? this.vehicleMap[characterId].join(', ') : null;
        },
        advancedData(character) {
            const _this = this;
            let data = [],
                max = 0;

            $.each(character.advanced, function (key, value) {
                key = _this.t('characters.result.advanced.' + key);

                data.push([key, value]);
            });

            data.push([this.t('characters.result.advanced.phone'), character.phoneNumber]);
            data.push([this.t('characters.result.gender'), this.$options.filters.formatGender(character.gender, this.t)]);
            data.push([this.t('characters.result.job'),
                (character.jobName ? character.jobName : this.t('global.none')) + ' / ' +
                (character.departmentName ? character.departmentName : this.t('global.none')) + ' / ' +
                (character.positionName ? character.positionName : this.t('global.none'))
            ]);
            data.push([this.t('characters.result.advanced.vehicles'), this.vehicleList(character.id)]);

            $.each(data, function (_, value) {
                if (value[0].length > max) {
                    max = value[0].length;
                }
            });

            let html = [];
            $.each(data, function (_, value) {
                html.push('<span class="block">' +
                    '<span class="font-semibold whitespace-pre">' + value[0].padEnd(max, ' ') + ':</span> ' +
                    (value[1] ? value[1] : 'N/A') +
                    '</span>');
            });

            return html.join('');
        }
    }
};
</script>
