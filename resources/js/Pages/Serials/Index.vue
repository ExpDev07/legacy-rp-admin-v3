<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('serials.title') }}
            </h1>
            <p>
                {{ t('serials.description') }}
            </p>
        </portal>

        <!-- Search -->
        <v-section>
            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <div class="w-1/2 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4 font-semibold" for="serial">
                                {{ t('serials.serial') }}
                            </label>
                            <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="serial" name="serial" placeholder="349428" v-model="filters.serial">
                        </div>
                        <div class="w-1/2 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4">&nbsp;</label>
                            <button class="px-5 py-2 font-semibold block w-full text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                                <span v-if="!isLoading">
                                    <i class="fas fa-search"></i>
                                    {{ t('serials.search') }}
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

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('serials.result') }}
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap text-left" v-if="result">
                    <!-- Item Type -->
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4">
                        <th class="px-6 py-4">{{ t('serials.table.item') }}</th>
                        <td class="px-6 py-3">
                            <img
                                :src="'/images/icons/items/' + result.item + '.png'"
                                :alt="result.item"
                                :title="result.item"
                                class="block w-inventory_slot h-inventory_slot"
                                v-if="result.item"
                            />
                        </td>
                    </tr>

                    <!-- Current Inventory -->
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4">
                        <th class="px-6 py-4">{{ t('serials.table.inventory') }}</th>
                        <td class="px-6 py-3">
                            <inertia-link
                                :title="t('inventories.view')"
                                class="text-indigo-600 dark:text-indigo-400"
                                :href="'/inventory/' + result.inventory"
                            >{{ result.inventory }}</inertia-link>
                        </td>
                    </tr>

                    <!-- Character -->
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4">
                        <th class="px-6 py-4">{{ t('serials.table.character') }}</th>
                        <td class="px-6 py-3" v-if="result.character">
                            <inertia-link
                                class="text-indigo-600 dark:text-indigo-400"
                                :href="'/players/' + result.character.steam + '/characters/' + result.character.id + '/edit'"
                            >{{ result.character.name }}</inertia-link>

                            <sup v-if="result.registered">{{ t('serials.registered') }}</sup>
                            <sup v-else>{{ t('serials.un_registered') }}</sup>
                        </td>
                        <td class="px-6 py-3" v-else>{{ t('serials.no_character') }}</td>
                    </tr>
                </table>
                <p v-else>
                    {{ t('serials.no_result') }}
                </p>
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
        result: {
            type: Object
        },
        filters: {
            serial: Number,
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
                await this.$inertia.replace('/serials', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'result' ],
                });
            } catch(e) {}

            this.isLoading = false;
        }
    }
}
</script>
