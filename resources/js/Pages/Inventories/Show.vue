<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white" v-if="inventory">
                {{ t('inventories.show.title') }} {{ inventory.title }} ({{ inventory.type }})
            </h1>
            <h1 class="dark:text-white" v-else>
                {{ t('inventories.show.error') }}
            </h1>
            <p v-if="snapshot">
                {{ t('inventories.show.snap_time', formatTime(snapshot.created), snapshot.created_by.player_name, formatTime(snapshot.expires)) }}
            </p>
        </portal>

        <!-- Table -->
        <v-section class="overflow-x-auto relative" v-if="inventory">
            <template #header>
                <h2>
                    {{ t('global.info') }}
                </h2>
                <p>
                    {{ t('inventories.show.type.' + inventory.type) }}
                </p>
                <a v-if="snapshotUrl" :href="'/inventory/snapshot/' + snapshotUrl" target="_blank" class="mt-3 text-white block px-5 py-2 border-2 rounded border-blue-500 bg-blue-600 dark:bg-blue-400">
                    {{ t('inventories.show.snap_url', snapshotUrl) }}
                </a>

                <button class="block px-2 py-1 text-center text-white absolute top-1 right-1 bg-blue-600 dark:bg-blue-400 rounded" :title="t('inventories.show.snapshot')" @click="createSnapshot" v-if="!snapshot && !snapshotUrl">
                    <i class="fas fa-camera"></i>
                </button>
                <inertia-link
                    class="block px-2 py-1 text-center text-white absolute top-1 right-10 bg-blue-600 dark:bg-blue-400 rounded"
                    :href="'/inventories/raw/' + inventory.title"
                    :title="t('inventories.view')"
                >
                    <i class="fas fa-briefcase"></i>
                </inertia-link>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-9 max-w-screen-lg"
                     v-if="inventory.type === 'character' || inventory.type === 'locker-police' || inventory.type === 'locker-mechanic' || inventory.type === 'locker-ems'">
                    <card v-if="inventory.character">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.character') }}
                            </h3>
                            <h4>
                                {{ inventory.character.first_name }} {{ inventory.character.last_name }}
                                (#{{ inventory.character.character_id }})
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span>
                                {{ $moment(inventory.character.date_of_birth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="inventory.character.character_deleted">
                                <span>{{ t('players.edit.deleted') }}:</span>
                                {{ $moment(inventory.character.character_deletion_timestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ inventory.character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="px-4 py-3 mb-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier + '/characters/' + inventory.character.character_id + '/edit'">
                                {{ t('inventories.show.view_character') }}
                            </inertia-link>
                            <inertia-link
                                class="px-4 py-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier">
                                {{ t('inventories.show.view_player') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-9 max-w-screen-lg"
                     v-else-if="inventory.type === 'trunk' || inventory.type === 'glovebox'">
                    <card v-if="inventory.vehicle">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.vehicle') }} ({{ inventory.more_info.type }})
                            </h3>
                            <h4>
                                {{ inventory.vehicle.model_name }}
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.vehicles.plate') }}:</span> {{ inventory.vehicle.plate }}
                            </h4>
                        </template>

                        <template>
                            <p v-html="t('players.vehicles.parked', inventory.more_info.garage)">
                                {{ t('players.vehicles.parked', inventory.more_info.garage) }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/vehicle/' + inventory.id"
                                v-if="(parseInt(inventory.id)+'') === inventory.id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/logs?action=%3DItem+Moved&details=' + inventory.title"
                                v-else
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                    <card v-else>
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.vehicle') }} ({{ inventory.more_info.type }})
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('inventories.show.plate_id') }}:</span> {{ inventory.id }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ t('inventories.show.unknown_vehicle') }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/logs?action=%3DItem+Moved&details=' + inventory.title"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                    <card v-if="inventory.character">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.owner') }}
                            </h3>
                            <h4>
                                {{ inventory.character.first_name }} {{ inventory.character.last_name }}
                                (#{{ inventory.character.character_id }})
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span>
                                {{ $moment(inventory.character.date_of_birth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="inventory.character.character_deleted">
                                <span>{{ t('players.edit.deleted') }}:</span>
                                {{ $moment(inventory.character.character_deletion_timestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ inventory.character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="px-4 py-3 mb-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier + '/characters/' + inventory.character.character_id + '/edit'">
                                {{ t('inventories.show.view_character') }}
                            </inertia-link>
                            <inertia-link
                                class="px-4 py-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier">
                                {{ t('inventories.show.view_player') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/character/' + inventory.character.character_id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-9 max-w-screen-lg"
                     v-else-if="inventory.type === 'property'">
                    <card v-if="inventory.property">
                        <template #header>
                            <h3 class="mb-2">
                                {{ inventory.property.property_address }}
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.cost') }}:</span> {{
                                    numberFormat(inventory.property.property_cost, 0, true)
                                }}
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.rent') }}:</span> {{
                                    numberFormat(inventory.property.property_income, 0, true)
                                }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{
                                    t('inventories.show.prop_description', inventory.property.property_renter, inventory.property.property_renter_cid)
                                }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/property/' + inventory.id"
                                v-if="(parseInt(inventory.id)+'') === inventory.id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                    <card v-else>
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.property') }}
                            </h3>
                        </template>

                        <template>
                            <p>
                                {{ t('inventories.show.unknown_prop') }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/logs?action=%3DItem+Moved&details=' + inventory.title"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                    <card v-if="inventory.character">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.prop_owner') }}
                            </h3>
                            <h4>
                                {{ inventory.character.first_name }} {{ inventory.character.last_name }}
                                (#{{ inventory.character.character_id }})
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span>
                                {{ $moment(inventory.character.date_of_birth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="inventory.character.character_deleted">
                                <span>{{ t('players.edit.deleted') }}:</span>
                                {{ $moment(inventory.character.character_deletion_timestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ inventory.character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="px-4 py-3 mb-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier + '/characters/' + inventory.character.character_id + '/edit'">
                                {{ t('inventories.show.view_character') }}
                            </inertia-link>
                            <inertia-link
                                class="px-4 py-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier">
                                {{ t('inventories.show.view_player') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/character/' + inventory.character.character_id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-9 max-w-screen-lg"
                     v-else-if="inventory.type === 'motel'">
                    <card v-if="inventory.character">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.motel_owner') }}
                            </h3>
                            <h4>
                                {{ inventory.character.first_name }} {{ inventory.character.last_name }}
                                (#{{ inventory.character.character_id }})
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span>
                                {{ $moment(inventory.character.date_of_birth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="inventory.character.character_deleted">
                                <span>{{ t('players.edit.deleted') }}:</span>
                                {{ $moment(inventory.character.character_deletion_timestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ inventory.character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="px-4 py-3 mb-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier + '/characters/' + inventory.character.character_id + '/edit'">
                                {{ t('inventories.show.view_character') }}
                            </inertia-link>
                            <inertia-link
                                class="px-4 py-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + inventory.character.steam_identifier">
                                {{ t('inventories.show.view_player') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/character/' + inventory.character.character_id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>

                <card v-if="cleanContents && $page.auth.player.isSuperAdmin" class="w-inventory_contents max-w-full">
                    <template #header>
                        <h3 class="mb-2" v-if="snapshot">
                            {{ t('inventories.show.snap_contents', formatTime(snapshot.created)) }}
                        </h3>
                        <h3 class="mb-2" v-else>
                            {{ t('inventories.show.contents') }}
                        </h3>
                    </template>

                    <template>
                        <div class="flex flex-wrap justify-between -mx-2">
                            <div
                                class="p-2 text-center w-inventory_slot h-inventory_slot break-words dark:bg-gray-700 m-2 rounded border cursor-default relative"
                                v-for="(item, slot) in cleanContents" :key="slot">

                                <img
                                    :src="'/images/icons/items/' + item.item + '.png'"
                                    :alt="item.item"
                                    :title="t('inventories.show.content_title', slot, item.amount, item.item)"
                                    class="block h-full w-full"
                                    v-if="item.item"
                                />

                                <span class="block font-semibold relative top-1/2 -translate-y-1/2 transform opacity-70"
                                      :title="t('inventories.show.content_empty', slot)" v-else>
                                    {{ t('inventories.show.empty') }}
                                </span>

                                <span class="block absolute -top-3 right-5 bg-gray-800 py-1 px-2 text-xs rounded" v-if="item.item">
                                    {{ item.amount }}
                                </span>

                                <span class="block absolute bottom-0 left-1/2 transform -translate-x-1/2 py-1 px-2 text-xs w-full overflow-hidden overflow-ellipsis whitespace-nowrap" v-if="item.item">
                                    {{ item.item }}
                                </span>

                                <button
                                    class="py-1 px-2 text-xs text-white bg-red-500 rounded hover:bg-red-600 absolute -top-3 -right-3"
                                    :title="t('inventories.show.empty_title', slot)"
                                    v-if="item.item"
                                    @click="confirmInventoryClear($event, slot)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template #footer>
                    </template>
                </card>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from '../../Layouts/App';
import VSection from '../../Components/Section';
import Pagination from '../../Components/Pagination';
import Card from './../../Components/Card';

export default {
    layout: Layout,
    components: {
        Pagination,
        VSection,
        Card,
    },
    methods: {
        async confirmInventoryClear(e, slot) {
            if (confirm(this.t('inventories.show.empty_confirm', slot))) {
                await this.$inertia.delete('/inventory/' + this.inventory.descriptor + '/clear/' + slot);
            }
        },
        async createSnapshot() {
            try {
                const data = await axios.post('/inventory/' + this.inventory.descriptor + '/createSnapshot');

                if (data.data && 'hash' in data.data) {
                    this.snapshotUrl = data.data.hash;
                    return;
                }
            } catch(e) {}

            alert('Failed to create inventory snapshot');
        },
        formatTime(t) {
            return this.$options.filters.formatTime(t * 1000, true);
        },
    },
    data() {
        let clean = {},
            maxSlot = 0;
        $.each(this.contents, function (_, item) {
            const key = item.inventory_slot;

            if (key > maxSlot) {
                maxSlot = key;
            }

            if (key in clean && clean[key].item === item.item_name) {
                clean[key].amount++;
            } else {
                clean[key] = {
                    item: item.item_name,
                    amount: 1
                };
            }
        });

        while (maxSlot === 0 || maxSlot % 5 !== 0) {
            maxSlot++;
        }

        let cleaned = {};
        for (let x = 1; x <= maxSlot; x++) {
            if (!(x in clean)) {
                cleaned[x] = {
                    item: null,
                    amount: 0
                };
            } else {
                cleaned[x] = clean[x];
            }
        }

        return {
            cleanContents: cleaned,
            snapshotUrl: ''
        }
    },
    props: {
        inventory: {
            type: Object,
            required: true,
        },
        snapshot: {
            type: Object,
            required: false,
        },
        contents: {
            type: Array,
            required: true,
        },
    }
};
</script>
