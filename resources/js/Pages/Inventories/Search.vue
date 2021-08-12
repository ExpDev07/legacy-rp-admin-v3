<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('inventories.search.title') }}
            </h1>
        </portal>

        <v-section>
            <template>
                <div class="flex flex-wrap mb-4">
                    <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                        <label class="block mb-3 font-semibold" for="inventory_type">
                            {{ t('inventories.search.type') }}
                        </label>
                        <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="inventory_type" v-model="filters.inventory_type">
                            <option value="character">{{ t('inventories.search.types.character') }}</option>
                            <option value="vehicle">{{ t('inventories.search.types.vehicle') }}</option>
                            <option value="evidence">{{ t('inventories.search.types.evidence') }}</option>
                            <option value="motel">{{ t('inventories.search.types.motel') }}</option>
                            <option value="property">{{ t('inventories.search.types.property') }}</option>
                        </select>
                    </div>

                    <div class="w-1/3 px-3 mobile:w-full mobile:mb-3" v-if="['character', 'motel', 'property'].includes(filters.inventory_type)">
                        <label class="block mb-3 font-semibold" for="inventory_cid">
                            {{ t('inventories.search.cid') }}
                        </label>
                        <input class="w-full px-4 py-3 bg-gray-200 dark:bg-gray-600 border rounded" id="inventory_cid" v-model="filters.inventory_cid" placeholder="16802">
                    </div>

                    <div class="w-1/3 px-3 mobile:w-full mobile:mb-3" v-if="filters.inventory_type === 'vehicle'">
                        <label class="block mb-3 font-semibold" for="inventory_plate_id">
                            {{ t('inventories.search.plate_id') }}
                        </label>
                        <input class="w-full px-4 py-3 bg-gray-200 dark:bg-gray-600 border rounded" id="inventory_plate_id" v-model="filters.inventory_plate_id" placeholder="24YNM199 / 1234">
                    </div>

                    <div class="w-1/3 px-3 mobile:w-full mobile:mb-3" v-if="filters.inventory_type === 'evidence'">
                        <label class="block mb-3 font-semibold" for="inventory_evidence_id">
                            {{ t('inventories.search.evidence_id') }}
                        </label>
                        <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="inventory_evidence_id" v-model="filters.inventory_evidence_id">
                            <option v-for="(pd, id) in pds" :key="id" :value="id">{{ pd }}</option>
                        </select>
                    </div>

                    <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                        <label class="block mb-3">&nbsp;</label>
                        <button class="block w-full px-4 py-3 font-semibold text-center text-white bg-success rounded dark:bg-dark-success" @click="refresh">
                            <span v-if="!isLoading">
                                <i class="fas fa-search"></i>
                                {{ t('global.do_search') }}
                            </span>
                                <span v-else>
                                <i class="fas fa-cog animate-spin"></i>
                                {{ t('global.loading') }}
                            </span>
                        </button>
                    </div>
                </div>
            </template>
        </v-section>

        <v-section>
            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('inventories.search.name') }}</th>
                        <th class="px-6 py-4">{{ t('inventories.search.id') }}</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="inventory in inventories" :key="inventory.id">
                        <td class="px-6 py-3 border-t mobile:block">{{ inventory.name }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ inventory.id }}</td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/inventory/' + inventory.id">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="inventories.length === 0">
                        <td class="px-6 py-6 text-center border-t mobile:block" colspan="100%">
                            {{ t('inventories.search.none') }}
                        </td>
                    </tr>
                </table>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from '../../Layouts/App';
import VSection from '../../Components/Section';
import Card from './../../Components/Card';
const PDLocations = require('../../data/pd.json');

export default {
    layout: Layout,
    components: {
        VSection,
        Card,
    },
    data() {
        return {
            isLoading: false,
            pds: PDLocations
        };
    },
    methods: {
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/search_inventory', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'inventories' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
    },
    props: {
        inventories: {
            type: Array,
            required: true,
        },
        filters: {
            inventory_type: String,
            inventory_cid: String,
            inventory_plate_id: String,
            inventory_evidence_id: String,
        }
    }
};
</script>
