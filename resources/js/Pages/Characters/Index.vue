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
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="character_id">
                                {{ t('characters.form.character_id') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="number" id="character_id" placeholder="16802" v-model="filters.character_id">
                        </div>
                        <!-- Name -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="name">
                                {{ t('characters.form.name') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="name" placeholder="Charlie Ives" v-model="filters.name">
                        </div>
                        <!-- Vehicle Plate -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="vehicle_plate">
                                {{ t('characters.form.plate') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="vehicle_plate" placeholder="95MBH817" v-model="filters.vehicle_plate">
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
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('characters.result.player') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.character_id') }}</th>
                        <th class="px-6 py-4">{{ t('characters.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('characters.result.gender') }}</th>
                        <th class="px-6 py-4">{{ t('characters.result.job') }}</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="character in characters.data" :key="character.id">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + character.steamIdentifier">
                                {{ character.player.playerName }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ character.id }}</td>
                        <td class="px-6 py-3 border-t">
                            {{ character.firstName }} {{ character.lastName }}
                        </td>
                        <td class="px-6 py-3 border-t">
                            {{ character.gender | formatGender(t) }}
                        </td>
                        <td class="px-6 py-3 border-t">
                            {{ character.jobName || t('global.none') }} /
                            {{ character.departmentName || t('global.none') }} /
                            {{ character.positionName || t('global.none') }}
                        </td>
                        <td class="px-6 py-3 border-t">
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
import throttle from "lodash/throttle";

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
        }
    },
    methods: {
        refresh: function () {
            this.$inertia.replace('/characters', {
                data: this.filters,
                preserveState: true,
                preserveScroll: true,
                only: [ 'characters' ],
            });
        },
    },
    watch: {
        filters: {
            deep: true,
            handler: throttle(function () {
                this.refresh();
            }, 150),
        }
    }
};
</script>
