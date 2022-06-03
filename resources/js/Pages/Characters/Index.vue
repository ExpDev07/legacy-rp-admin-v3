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
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="character_id">
                                {{ t('characters.form.character_id') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="number" id="character_id" placeholder="83118" v-model="filters.character_id">
                        </div>
                        <!-- Name -->
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="name">
                                {{ t('characters.form.name') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="name" placeholder="Lela Law" v-model="filters.name">
                        </div>
                        <!-- Vehicle Plate -->
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="vehicle_plate">
                                {{ t('characters.form.plate') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="vehicle_plate" placeholder="23FTW355" v-model="filters.vehicle_plate">
                        </div>
                        <!-- Date of Birth -->
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="vehicle_plate">
                                {{ t('characters.form.dob') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="dob" placeholder="1998-03-04" v-model="filters.dob">
                        </div>
                        <!-- Phone Number -->
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="phone">
                                {{ t('characters.form.phone') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="phone" placeholder="723-4797" v-model="filters.phone">
                        </div>
                        <!-- Job -->
                        <div class="w-1/2 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="job">
                                {{ t('characters.form.job') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="job" placeholder="Law Enforcement SASP Cadet" v-model="filters.job">
                        </div>
                        <!-- Deletion status -->
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="deleted">
                                {{ t('characters.form.deleted') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <select class="block w-full px-4 py-3 mb-3 bg-gray-200 dark:bg-gray-600 border rounded" id="deleted" name="deleted" v-model="filters.deleted">
                                <option value="all">{{ t('global.all') }}</option>
                                <option value="yes">{{ t('global.yes') }}</option>
                                <option value="no">{{ t('global.no') }}</option>
                            </select>
                        </div>
                        <!-- Description -->
                        <div class="w-full px-3 mt-3">
                            <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">* {{ t('global.search.exact') }}</small>
                            <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">** {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
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
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('characters.result.player') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.character_id') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.phone') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('characters.result.gender') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.dob') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.job') }}</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="character in characters.data" :key="character.id">
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + character.steamIdentifier">
                                {{ playerName(character.steamIdentifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">{{ character.id }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ character.phoneNumber }}</td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ character.firstName }} {{ character.lastName }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ character.gender | formatGender(t) }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ character.dateOfBirth }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            {{ character.jobName || t('global.none') }} /
                            {{ character.departmentName || t('global.none') }} /
                            {{ character.positionName || t('global.none') }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/players/' + character.steamIdentifier + '/characters/' + character.id + '/edit'">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
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
            dob: String,
            phone: String,
            job: String,
            deleted: String,
        },
        playerMap: {
            type: Object,
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
                await this.$inertia.replace('/characters', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'characters', 'playerMap', 'time' ],
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
